<?php

namespace App\Http\Controllers;

use App\Enums\ListingType;
use App\Http\Resources\CarMakeResource;
use App\Http\Resources\ListingResource;
use App\Models\CarMake;
use App\Models\Listing;
use App\Traits\HasApiResponse;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    use HasApiResponse;
    public function home()
    {
        $sections = [];
        foreach (ListingType::cases() as $type) {
            $listings = Listing::where('type', $type->value)
                ->latest()
                ->take(value: 5)
                ->with(['user', 'images', 'listable'])
                ->get()
                ->map(function ($item) {
                    return ListingResource::make($item);
                });

            $sections[] = [
                'type' => $type->value,
                'listings' => $listings,
            ];
        }
        return $this->success(
            [
                "carousel" => [
                    "https://www.autoshippers.co.uk/blog/wp-content/uploads/bugatti-centodieci-1024x576.jpg",
                    "https://www.autoshippers.co.uk/blog/wp-content/uploads/bugatti-centodieci-1024x576.jpg",
                    "https://www.autoshippers.co.uk/blog/wp-content/uploads/bugatti-centodieci-1024x576.jpg",
                    "https://www.autoshippers.co.uk/blog/wp-content/uploads/bugatti-centodieci-1024x576.jpg",
                ],
                "types" => ListingType::values(),
                "sections" => $sections
            ]
        );
    }
    public function transmissionTypes()
    {
        return $this->success([
            'transmissions' => ['automatic', 'manual'],
        ]);
    }
    public function fuelTypes()
    {
        return $this->success([
            'fuels' => ['petrol', 'diesel', 'electric', 'hybrid'],
        ]);
    }
    public function steeringSides()
    {
        return $this->success([
            'steering_sides' => ['right-hand', 'left-hand'],
        ]);
    }
    public function colors()
    {
        return $this->success([
            'colors' => [
                'black',
                'white',
                'silver',
                'gray',
                'blue',
                'red',
                'green',
                'brown',
                'beige',
                'gold',
                'orange',
                'purple',
                'yellow',
                'maroon',
                'other'
            ],
        ]);
    }

    public function carMakes()
    {
        return $this->success(CarMakeResource::collection(CarMake::get()));
    }

    public function conditions()
    {
        return $this->success([
            'conditions' => [
                'new',
                'used',
            ],
        ]);
    }
    public function sparePartCategories()
    {
        return $this->success([
            'categories' => [
                'engine',
                'transmission',
                'brakes',
                'suspension',
                'electrical',
                'interior',
                'exterior',
                'cooling',
                'fuel_system',
                'body',
                'lighting',
                'other'
            ],
        ]);
    }

    public function bodyTypes()
    {
        return $this->success([
            'body_types' => [
                'sedan',
                'hatchback',
                'suv',
                'coupe',
                'convertible',
                'pickup',
                'van',
                'wagon',
                'crossover',
                'other'
            ],
        ]);
    }
}
