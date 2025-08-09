<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PublishQuotaService
{
    public function reservePerAd(int $userId, int $perAdFeatureId): array
    {
        $credit = DB::table('ad_credits')
            ->where('user_id', $userId)
            ->where('per_ad_feature_id', $perAdFeatureId)
            ->where('quantity', '>', 0)
            ->lockForUpdate()
            ->first();

        if (!$credit) {
            throw new \RuntimeException('مافيش رصيد إعلانات بالقطعة من النوع ده.');
        }

        $feature = DB::table('per_ad_features')->find($perAdFeatureId);
        if (!$feature) {
            throw new \RuntimeException('نوع الإعلان غير موجود.');
        }

        DB::table('ad_credits')->where('id', $credit->id)->decrement('quantity', 1);

        return [
            'level' => $feature->level,
            'days' => (int) $feature->duration_days,
            'source' => 'per_ad',
            'source_id' => $credit->id,
        ];
    }

    public function reserveSubscription(int $userId): array
    {
        $sub = DB::table('subscriptions')
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->lockForUpdate()
            ->first();

        if (!$sub || (int) $sub->remaining_ads <= 0) {
            throw new \RuntimeException('اشتراكك غير نشط أو رصيد الإعلانات خلص.');
        }

        $feature = DB::table('subscription_features')
            ->join('packages', 'subscription_features.package_id', '=', 'packages.id')
            ->where('packages.id', $sub->package_id)
            ->select('subscription_features.duration_days')
            ->first();

        $days = (int) ($feature->duration_days ?? 30);

        DB::table('subscriptions')->where('id', $sub->id)->decrement('remaining_ads', 1);

        return [
            'level' => 'basic',    
            'days' => $days,
            'source' => 'subscription',
            'source_id' => $sub->id,
        ];
    }
}
