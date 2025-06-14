<?php

namespace App\Support;
use App\Enums\ListingType;
use App\Support\ListingTypeRules\SparePartRules;
use App\Support\ListingTypeRules\VehicleRules;
class ListingRuleResolver
{
    public static function resolve(string $type, array $input): array
    {
        return match ($type) {
            ListingType::VEHICLE->value => VehicleRules::rules($input),
            ListingType::SPARE_PARTS->value => SparePartRules::rules($input),
            default => []
        };
    }
}