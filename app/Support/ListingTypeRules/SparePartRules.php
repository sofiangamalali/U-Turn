<?php
namespace App\Support\ListingTypeRules;

class SparePartRules
{


    public static function rules(array $input): array
    {
        return [
            'spare_parts.stock_quantity' => ['required', 'integer', 'min:1'],
            'spare_parts.car_make_id' => ['nullable', 'exists:car_makes,id'],
            'spare_parts.car_model_id' => ['nullable', 'exists:car_models,id'],
            'spare_parts.compatible_year_from' => ['nullable', 'digits:4', 'integer', 'min:1900', 'max:' . date('Y')],
            'spare_parts.compatible_year_to' => ['nullable', 'digits:4', 'integer', 'min:1900', 'max:' . date('Y')],
            'spare_parts.category' => ['required', 'in:engine,transmission,brakes,suspension,electrical,interior,exterior,cooling,fuel_system,body,lighting,other'],
            'spare_parts.condition' => ['required', 'in:new,used'],
        ];
    }
}