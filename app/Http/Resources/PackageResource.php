<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'per_ad_features' => $this->when(
                $this->type === 'pay_per_ad',
                PerAdFeatureResource::collection($this->perAdFeatures)
            ),
            'subscription_features' => $this->when(
                $this->type === 'subscription',
                SubscriptionFeatureResource::collection($this->subscriptionFeatures)
            ),
        ];
    }
}
