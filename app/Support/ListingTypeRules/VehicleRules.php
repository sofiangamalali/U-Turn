<?php
namespace App\Support\ListingTypeRules;

use App\Rules\MakeOwnsModel;
class VehicleRules
{


    public static function rules(array $input): array
    {
        return [
            'vehicle.car_make_id' => 'required|exists:car_makes,id',
            'vehicle.car_model_id' => [
                'nullable',
                'exists:car_models,id',
                new MakeOwnsModel($input['vehicle']['car_make_id'] ?? 0),
            ],
            'vehicle.manufacture_year' => 'required|digits:4|integer',
            'vehicle.mileage' => 'nullable|integer',
            'vehicle.transmission_type' => 'required|in:automatic,manual',
            'vehicle.fuel_type' => 'required|in:petrol,diesel,electric,hybrid',
            'vehicle.exterior_color' => 'required|in:black,white,silver,gray,blue,red,green,brown,beige,gold,orange,purple,yellow,maroon,other',
            'vehicle.interior_color' => 'required|in:black,white,silver,gray,blue,red,green,brown,beige,gold,orange,purple,yellow,maroon,other',
            'vehicle.doors' => 'required|integer|min:2|max:6',
            'vehicle.seating_capacity' => 'nullable|integer',
            'vehicle.horsepower' => 'nullable|integer',
            'vehicle.steering_side' => 'required|in:left-hand,right-hand',
        ];
    }
}