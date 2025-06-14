<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SparePartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'make' => $this->make->name ?? null,
            'model' => $this->model->name ?? null,
            'compatible_year_from' => $this->compatible_year_from,
            'compatible_year_to' => $this->compatible_year_to,
            'category' => $this->category,
            'condition' => $this->condition,
        ];
    }
}
