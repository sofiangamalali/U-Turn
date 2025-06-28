<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'type' => $this->type,
            $this->mergeWhen($this->type === 'text', ['message' => $this->message]),
            $this->mergeWhen($this->type === 'image', ['image' => new ImageResource($this->image)]),
            $this->mergeWhen($this->type === 'voice', ['voice' => new VoiceResource($this->voice)]),
            'sent_at' => $this->sent_at,
            'read_at' => $this->read_at,
            'is_current_user' => $this->is_current_user,
        ];
    }
}
