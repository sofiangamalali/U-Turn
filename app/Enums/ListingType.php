<?php
namespace App\Enums;

enum ListingType: string
{
    case VEHICLE = 'vehicle';
    case SPARE_PARTS = 'spare_parts';



    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
