<?php
namespace App\Services\ListingTypesHandlers;

use App\Contracts\ListingTypeHandlerInterface;
use App\Models\Vehicle;

class VehicleHandler implements ListingTypeHandlerInterface
{

    public function create(array $data): array
    {
        return [
            'listable_type' => Vehicle::class,
            'listable_id' => Vehicle::create($data['vehicle'])->id,
        ];
    }
    public function update(array $data, $model): array
    {
        $model->update($data['vehicle']);
        return [
            'listable_type' => Vehicle::class,
            'listable_id' => $model->id,
        ];
    }
}


