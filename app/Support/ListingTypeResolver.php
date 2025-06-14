<?php

namespace App\Support;

use App\Enums\ListingType;
use App\Models\SparePart;
use App\Models\Vehicle;
use App\Services\ListingTypesHandlers\SparePartHandler;
use App\Services\ListingTypesHandlers\VehicleHandler;

class ListingTypeResolver
{
    public static function resolve(string $type)
    {
        return match ($type) {
            ListingType::VEHICLE->value => new VehicleHandler(),
            ListingType::SPARE_PARTS->value => new SparePartHandler(),

        };
    }
    public static function getModelClass(string $type): string
    {
        return match ($type) {
            ListingType::VEHICLE->value => Vehicle::class,
            ListingType::SPARE_PARTS->value => SparePart::class,
            default => throw new \InvalidArgumentException("Unknown listing type: $type")
        };
    }
}
