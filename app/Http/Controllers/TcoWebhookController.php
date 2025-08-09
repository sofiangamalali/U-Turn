<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\TcoClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TcoWebhookController extends Controller
{
    public function handle(Request $request, TcoClient $tco)
    {
        $p = $request->all();

     
        if (!$tco->validateIpn($p)) {
            Log::warning('2CO IPN invalid', $p);
            return response('Invalid', 400);
        }

        $event = $p['MESSAGE_TYPE'] ?? $p['message_type'] ?? '';
        $refNo = $p['REFNO'] ?? null;       // رقم الطلب في 2CO
        $extRef = $p['REFNOEXT'] ?? null;    // ext_ref بتاعنا
        $subRef = $p['SUBSCRIPTION_REF'] ?? null;
        $amount = $p['PAYMENT_AMOUNT'] ?? $p['AMOUNT'] ?? null;
        $currency = $p['PAYMENT_CURRENCY'] ?? $p['CURRENCY'] ?? null;

        try {
            DB::transaction(function () use ($event, $refNo, $extRef, $subRef, $amount, $currency, $p) {

                $order = $extRef ? DB::table('orders')->where('ext_ref', $extRef)->lockForUpdate()->first() : null;
                if (!$order) {
                    Log::warning('Order not found for IPN', ['ext_ref' => $extRef]);
                    return;
                }

                // INITIAL PAYMENT (per-ad أو أول اشتراك)
                if (in_array($event, ['ORDER_CREATED', 'PAYMENT_COMPLETE'])) {
                    DB::table('orders')->where('id', $order->id)->update([
                        'provider_ref' => $refNo,
                        'status' => 'paid',
                        'updated_at' => now(),
                    ]);

                    // سجّل الدفع
                    DB::table('payments')->updateOrInsert(
                        ['provider_order_ref' => $refNo, 'event' => 'initial'],
                        [
                            'order_id' => $order->id,
                            'amount' => $amount,
                            'currency' => $currency,
                            'status' => 'paid',
                            'payload' => json_encode($p),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );

                    if ($order->context_type === 'per_ad') {
                        // نزود كريدت إعلان واحد (بمستوى الـ feature ومدته هنطبّقهم في وقت النشر)
                        DB::table('ad_credits')->insert([
                            'user_id' => $order->user_id,
                            'per_ad_feature_id' => $order->context_id,
                            'quantity' => 1,
                            'expires_at' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    if ($order->context_type === 'subscription') {
                        // نفعّل/ننشيء اشتراك
                        $feature = DB::table('subscription_features')->find($order->context_id);
                        $durationDays = (int) $feature->duration_days ?: 30;

                        // ابدا من الآن لغاية الآن + المدة
                        $start = Carbon::now();
                        $end = Carbon::now()->addDays($durationDays);

                        // upsert اشتراك للمستخدم
                        $exists = DB::table('subscriptions')->where('user_id', $order->user_id)
                            ->where('package_id', $feature->package_id)->lockForUpdate()->first();

                        if ($exists) {
                            DB::table('subscriptions')->where('id', $exists->id)->update([
                                'started_at' => $start,
                                'expires_at' => $end,
                                'is_active' => true,
                                'provider' => '2checkout',
                                'provider_subscription_id' => $subRef,
                                'status' => 'active',
                                'max_ads' => $feature->max_ads,
                                'remaining_ads' => $feature->max_ads,
                                'updated_at' => now(),
                            ]);
                        } else {
                            DB::table('subscriptions')->insert([
                                'user_id' => $order->user_id,
                                'package_id' => $feature->package_id,
                                'started_at' => $start,
                                'expires_at' => $end,
                                'is_active' => true,
                                'provider' => '2checkout',
                                'provider_subscription_id' => $subRef,
                                'status' => 'active',
                                'max_ads' => $feature->max_ads,
                                'remaining_ads' => $feature->max_ads,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }

                // RECURRING SUCCESS (تجديد اشتراك)
                if ($event === 'RECURRING_INSTALLMENT_SUCCESS' && $subRef) {
                    $sub = DB::table('subscriptions')->where('provider_subscription_id', $subRef)->lockForUpdate()->first();
                    if ($sub) {
                        // جدّد الفترة بنفس المدة
                        $feature = DB::table('subscription_features')
                            ->join('packages', 'subscription_features.package_id', '=', 'packages.id')
                            ->where('packages.id', $sub->package_id)
                            ->select('subscription_features.duration_days', 'subscription_features.max_ads')
                            ->first();

                        $durationDays = (int) ($feature->duration_days ?? 30);
                        $newStart = Carbon::parse($sub->expires_at ?? now());
                        $newEnd = (clone $newStart)->addDays($durationDays);

                        DB::table('subscriptions')->where('id', $sub->id)->update([
                            'status' => 'active',
                            'is_active' => true,
                            'started_at' => $newStart,
                            'expires_at' => $newEnd,
                            'remaining_ads' => $feature->max_ads, // reset شهري
                            'updated_at' => now(),
                        ]);

                        if ($refNo) {
                            DB::table('payments')->updateOrInsert(
                                ['provider_order_ref' => $refNo, 'event' => 'recurring'],
                                [
                                    'order_id' => $order?->id,
                                    'amount' => $amount,
                                    'currency' => $currency,
                                    'status' => 'paid',
                                    'payload' => json_encode($p),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]
                            );
                        }
                    }
                }

                // RECURRING FAILED
                if ($event === 'RECURRING_INSTALLMENT_FAILED' && $subRef) {
                    DB::table('subscriptions')->where('provider_subscription_id', $subRef)
                        ->update(['status' => 'past_due', 'is_active' => false, 'updated_at' => now()]);
                }

                // STATUS CHANGED (إلغاء/إيقاف)
                if ($event === 'SUBSCRIPTION_STATUS_CHANGED' && $subRef) {
                    $new = strtolower($p['STATUS'] ?? $p['status'] ?? '');
                    $local = str_contains($new, 'cancel') ? 'canceled'
                        : (str_contains($new, 'active') ? 'active' : 'incomplete');
                    DB::table('subscriptions')->where('provider_subscription_id', $subRef)
                        ->update(['status' => $local, 'is_active' => $local === 'active', 'updated_at' => now()]);
                }
            });

            // لازم نرجّع توكن رد نصّي
            return response($tco->ipnResponseToken($p), 200)->header('Content-Type', 'text/plain');

        } catch (\Throwable $e) {
            Log::error('2CO IPN error: ' . $e->getMessage(), $p);
            return response('ERR', 500);
        }
    }
}
