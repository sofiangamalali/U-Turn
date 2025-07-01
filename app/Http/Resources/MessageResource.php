<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'message' => $this->buildMessageContent(),
            'sent_at' => $this->sent_at,
            'read_at' => $this->read_at,
            'is_current_user' => $this->is_current_user,
        ];
    }

    protected function buildMessageContent()
    {
        return match ($this->type) {
            'text' => $this->message,
            'image' => $this->image ? asset($this->getImageFolder() . '/' . $this->image->file_path) : null,
            'voice' => $this->voice ? asset('voices/' . $this->voice->file_path) : null,
            default => null,
        };
    }

    protected function getImageFolder(): string
    {
        return Str::plural(strtolower(class_basename($this->image->imageable_type)));
    }
}
