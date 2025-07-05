<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'make' => $this->make->name ?? null,
            'model' => $this->model->name ?? null,
            'year' => $this->manufacture_year,
            'mileage' => $this->mileage,
            'fuel' => $this->fuel_type,
            'transmission' => $this->transmission_type,
            'color' => $this->exterior_color,
            'interior' => $this->interior_color,
            'horsepower' => $this->horsepower,
            'doors' => $this->doors,
            'seats' => $this->seating_capacity,
            'steering' => $this->steering_side,
            'body_type' => $this->body_type,
            'consumption' => $this->consumption
        ];
    }
}
