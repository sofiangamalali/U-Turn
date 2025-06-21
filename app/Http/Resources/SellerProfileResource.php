<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerProfileResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'profile_image' => new ImageResource($this->whenLoaded('profileImage')),
            'listings' => ListingResource::collection($this->whenLoaded('listings')),
            'rating' => [
                'average' => 4.5,
                'total' => 210,
                'breakdown' => [
                    5 => 586,
                    4 => 145,
                    3 => 89,
                    2 => 78,
                    1 => 45
                ]
            ]
        ];
    }
}
