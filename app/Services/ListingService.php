<?php

namespace App\Services;

use App\Enums\ListingType;
use App\Support\ListingTypeResolver;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class ListingService
{

    public function getAll()
    {
        return Listing::with(['user', 'images', 'listable'])
            ->latest()
            ->get();
    }


    public function filteredPaginated(Request $request, int $perPage = 15)
    {
        $query = Listing::with(['user', 'images', 'listable'])
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->orderByRaw("FIELD(level, 'premium','highlight','basic')")
            ->latest('published_at');

        if ($request->filled('type')) {
            $query->where('type', $request->type);

            $query->whereHasMorph('listable', [
                ListingTypeResolver::getModelClass($request->type)
            ], function (Builder $q) use ($request) {

                switch ($request->type) {
                    case ListingType::VEHICLE->value:
                        if ($request->filled('transmission_type')) {
                            $q->where('transmission_type', $request->transmission_type);
                        }
                        if ($request->filled('fuel_type')) {
                            $q->where('fuel_type', $request->fuel_type);
                        }
                        if ($request->filled('steering_side')) {
                            $q->where('steering_side', $request->steering_side);
                        }
                        if ($request->filled('car_model_id')) {
                            $q->where('car_model_id', $request->car_model_id);
                        }
                        if ($request->filled('car_make_id')) {
                            $q->where('car_make_id', $request->car_make_id);
                        }
                        break;

                    case ListingType::SPARE_PARTS->value:
                        if ($request->filled('category')) {
                            $q->where('category', $request->category);
                        }
                        if ($request->filled('condition')) {
                            $q->where('condition', $request->condition);
                        }
                        if ($request->filled('compatible_year_from')) {
                            $q->where('compatible_year_from', '>=', $request->compatible_year_from);
                        }
                        if ($request->filled('compatible_year_to')) {
                            $q->where('compatible_year_to', '<=', $request->compatible_year_to);
                        }
                        break;

                    default:
                        break;
                }
            });
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->keyword . '%')
                  ->orWhere('description', 'like', '%' . $request->keyword . '%');
            });
        }

        return $query->paginate($perPage)->appends($request->query());
    }

    public function findById($id)
    {
        return Listing::with(['user', 'listable', 'images'])->findOrFail($id);
    }

    /**
     * إنشاء إعلان + حجز الرصيد داخل Transaction:
     * - لو فيه per_ad_feature_id → يستهلك كريدت من ad_credits.
     * - غير كده → يستهلك من اشتراك نشط (subscriptions.remaining_ads).
     * - يثبت level / published_at / expires_at / publish_source.
     *
     * ملاحظة: لازم يكون عندك PublishQuotaService بالواجهتين:
     *   reservePerAd($userId, $perAdFeatureId) : array{level,days,source,source_id}
     *   reserveSubscription($userId)           : array{level,days,source,source_id}
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $userId        = auth()->id();
            $quotaService  = app(\App\Services\PublishQuotaService::class);

            // 1) حدّد مصدر النشر واحجز الرصيد
            if (!empty($data['per_ad_feature_id'])) {
                $quota = $quotaService->reservePerAd($userId, (int) $data['per_ad_feature_id']);
            } else {
                $quota = $quotaService->reserveSubscription($userId);
            }

            // 2) أنشئ listable model حسب الـ type
            $handler      = ListingTypeResolver::resolve($data['type']);
            $listableData = $handler->create($data);

            // 3) بيانات الإعلان العامة (استبعد الصور والحقول الخاصة)
            $filteredData = collect($data)->except([
                ...ListingType::values(),
                'images',
                'per_ad_feature_id',
                'use_subscription',
                'level', // ما نخليش المستخدم يفرض Level أعلى من الكوتا
            ])->all();

            $now   = now();
            $ends  = now()->clone()->addDays($quota['days']);
            $level = $quota['level']; // basic|highlight|premium

            $listing = Listing::create(array_merge(
                $filteredData,
                $listableData,
                [
                    'user_id'           => $userId,
                    'status'            => 'active',
                    'level'             => $level,
                    'published_at'      => $now,
                    'expires_at'        => $ends,
                    'publish_source'    => $quota['source'],     // per_ad | subscription
                    'publish_source_id' => $quota['source_id'],  // id الكريدت أو الاشتراك
                ]
            ));

            if (!empty($data['images'])) {
                $listing->uploadImages($data['images']);
            }

            return $listing;
        });
    }

    public function update(Listing $listing, array $data): Listing
    {
        return DB::transaction(function () use ($listing, $data) {
            $handler = ListingTypeResolver::resolve($listing->type);
            $model   = $listing->listable;

            $listableData = $handler->update($data, $model);

            // تحديث بيانات الإعلان (من غير ما نغيّر publish_source أو الرصيد)
            $listing->update(array_merge($data, $listableData));

            if (!empty($data['images'])) {
                $listing->updateImages($data['images']);
            }

            return $listing;
        });
    }

    public function delete(Listing $listing): bool
    {
        return DB::transaction(function () use ($listing) {
            $listing->deleteImages();
            $model = $listing->listable;
            $model->delete();
            return $listing->delete();
        });
    }
}
