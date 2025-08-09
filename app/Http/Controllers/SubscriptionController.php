<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PerAdFeature;
use App\Models\SubscriptionFeature;
use App\Services\TcoClient;
use DB;
use Illuminate\Http\Request;
use Str;



class SubscriptionController extends Controller
{
    public function __construct(protected TcoClient $tcoClient)
    {
    }

    public function perAd(Request $req, TcoClient $tco)
    {
        $data = $req->validate([
            'per_ad_feature_id' => 'required|exists:per_ad_features,id'
        ]);
        $user = $req->user();
        $feature = PerAdFeature::findOrFail($data['per_ad_feature_id']);
        $package = Package::findOrFail($feature->package_id);

        // لو مجاني: ما فيش دفع – نزود كريدت مباشرة
        if ((int) $feature->is_free === 1 || (float) $feature->price == 0.0) {
            DB::table('ad_credits')->insert([
                'user_id' => $user->id,
                'per_ad_feature_id' => $feature->id,
                'quantity' => 1,
                'expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return response()->json(['free' => true, 'message' => 'تم إضافة إعلان مجاني لحسابك']);
        }

        // 1) أنشئ order pending
        $extRef = 'PAD-' . now()->timestamp . '-U' . $user->id . '-' . Str::random(6);
        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $user->id,
            'context_type' => 'per_ad',
            'context_id' => $feature->id,
            'ext_ref' => $extRef,
            'status' => 'pending',
            'amount' => $feature->price,
            'currency' => 'USD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        $params = [
            'merchant' => env('TCO_SELLER_ID'),
            'dynamic' => 1,
            'test' => (int) env('TCO_TEST', 1),

            'prod' => $package->name . ' - ' . $feature->label,
            'type' => 'PRODUCT',
            'qty' => 1,
            'price' => number_format($feature->price, 2, '.', ''),
            'currency' => 'USD',
            'tangible' => 0,

            // per-ad = مش اشتراك → ما نحطّش recurrence
            // ربط
            'order-ext-ref' => $extRef,
            'customer-ext-ref' => (string) $user->id,

            // نجاح فقط
            'return-url' => config('app.url') . '/payments/2co/success',
            'return-type' => 'redirect',

            // معلومة للواجهة
            'x-feature-id' => $feature->id,
            'x-order-id' => $orderId,
        ];

        $params['signature'] = $tco->signBuyLink($params);
        $url = 'https://secure.2checkout.com/checkout/buy/?' . http_build_query($params);

        return response()->json(['url' => $url]);
    }

    public function subscription(Request $req, TcoClient $tco)
    {
        $data = $req->validate([
            'subscription_feature_id' => 'required|exists:subscription_features,id'
        ]);
        $user = $req->user();
        $feature = SubscriptionFeature::findOrFail($data['subscription_feature_id']);
        $package = Package::findOrFail($feature->package_id);

        // 1) order pending
        $extRef = 'SUB-' . now()->timestamp . '-U' . $user->id . '-' . Str::random(6);
        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $user->id,
            'context_type' => 'subscription',
            'context_id' => $feature->id,
            'ext_ref' => $extRef,
            'status' => 'pending',
            'amount' => $feature->price,
            'currency' => 'USD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $intervalCount = 1;  
        $intervalUnit = 'MONTH';
        if ((int) $feature->duration_days === 7) {
            $intervalCount = 1;
            $intervalUnit = 'WEEK';
        }
        if ((int) $feature->duration_days === 30) {
            $intervalCount = 1;
            $intervalUnit = 'MONTH';
        }
        if ((int) $feature->duration_days >= 365) {
            $intervalCount = 1;
            $intervalUnit = 'YEAR';
        }

        $params = [
            'merchant' => env('TCO_SELLER_ID'),
            'dynamic' => 1,
            'test' => (int) env('TCO_TEST', 1),

            'prod' => $package->name . ' - ' . $feature->title,
            'type' => 'PRODUCT',
            'qty' => 1,
            'price' => number_format($feature->price, 2, '.', ''),
            'currency' => 'USD',
            'tangible' => 0,

            'recurrence' => "{$intervalCount}:{$intervalUnit}",
            'duration' => 'FOREVER',
            'renewal-price' => number_format($feature->price, 2, '.', ''),

            'order-ext-ref' => $extRef,
            'customer-ext-ref' => (string) $user->id,

            'return-url' => config('app.url') . '/payments/2co/success',
            'return-type' => 'redirect',

            'x-feature-id' => $feature->id,
            'x-order-id' => $orderId,
        ];

        $params['signature'] = $tco->signBuyLink($params);
        $url = 'https://secure.2checkout.com/checkout/buy/?' . http_build_query($params);

        return response()->json(['url' => $url]);
    }

}
