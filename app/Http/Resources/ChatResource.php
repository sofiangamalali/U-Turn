<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
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
            'unread_counts' => (int)$this->unread_counts,
            'name' => $this->name,
            'blocked' => (boolean) $this->blocked,
            'last_message' => new MessageResource($this->whenLoaded('lastMessage')),
            'listing' => ListingResource::make($this->whenLoaded('listing')),
            'participants' => UserResource::collection($this->whenLoaded('participants')),

        ];
    }
}
