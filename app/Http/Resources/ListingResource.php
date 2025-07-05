<?php

namespace App\Http\Resources;

use App\Support\ListingResourceResolver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'type' => $this->type,
            'date' => $this->created_at,
            'user' => UserResource::make($this->whenLoaded('user')),
            'listable' => $this->whenLoaded('listable', fn() => ListingResourceResolver::resolve($this->type, $this->listable)),
            'first_image' => ImageResource::make($this->first_image),
            'images' => ImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
