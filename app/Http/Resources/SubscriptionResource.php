<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'started_at' => $this->started_at,
            'expires_at' => $this->expires_at,
            'is_active' => (bool) $this->is_active,
            'package' => new PackageResource($this->whenLoaded('package')),
        ];
    }
}
