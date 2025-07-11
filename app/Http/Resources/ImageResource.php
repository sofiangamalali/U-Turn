<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Str;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'url' => asset('' . $this->getFolderName() . '/' . $this->file_path),
        ];
    }
    protected function getFolderName(): string
    {
        return Str::plural(strtolower(class_basename($this->imageable_type)));
    }
}
