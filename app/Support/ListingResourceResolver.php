<?php
namespace App\Support;

use App\Enums\ListingType;
use App\Http\Resources\SparePartResource;
use App\Http\Resources\VehicleResource;
class ListingResourceResolver
{
    public static function resolve(string $type, $listable)
    {
        return match ($type) {
            ListingType::VEHICLE->value => new VehicleResource($listable),
            ListingType::SPARE_PARTS->value => new SparePartResource($listable),
            default => null
        };

    }
}