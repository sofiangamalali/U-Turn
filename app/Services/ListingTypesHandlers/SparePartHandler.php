<?php
namespace App\Services\ListingTypesHandlers;

use App\Contracts\ListingTypeHandlerInterface;
use App\Models\SparePart;
use App\Models\Vehicle;

class SparePartHandler implements ListingTypeHandlerInterface
{

    public function create(array $data): array
    {
        return [
            'listable_type' => SparePart::class,
            'listable_id' => SparePart::create($data['spare_parts'])->id,
        ];
    }
    public function update(array $data, $model): array
    {
        $model->update($data['spare_parts']);
        return [
            'listable_type' => SparePart::class,
            'listable_id' => $model->id,
        ];
    }
}


