<?php

namespace Database\Seeders;

use App\Models\CarMake;
use App\Models\CarModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarMakeModel extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $make = CarMake::create(['name' => 'Toyota']);
        CarModel::create(['name' => 'Corolla', 'car_make_id' => $make->id]);
        CarModel::create(['name' => 'Camry', 'car_make_id' => $make->id]);
        CarModel::create(['name' => 'Land Cruiser', 'car_make_id' => $make->id]);

        $make = CarMake::create(['name' => 'BMW']);
        CarModel::create(['name' => 'X5', 'car_make_id' => $make->id]);
        CarModel::create(['name' => '3 Series', 'car_make_id' => $make->id]);
        CarModel::create(['name' => 'X3', 'car_make_id' => $make->id]);

        $make = CarMake::create(['name' => 'Hyundai']);
        CarModel::create(['name' => 'Elantra', 'car_make_id' => $make->id]);
        CarModel::create(['name' => 'Tucson', 'car_make_id' => $make->id]);
        CarModel::create(['name' => 'Sonata', 'car_make_id' => $make->id]);

        $make = CarMake::create(['name' => 'Mercedes']);
        CarModel::create(['name' => 'C-Class', 'car_make_id' => $make->id]);
        CarModel::create(['name' => 'E-Class', 'car_make_id' => $make->id]);
        CarModel::create(['name' => 'GLC', 'car_make_id' => $make->id]);

        $make = CarMake::create(['name' => 'Nissan']);
        CarModel::create(['name' => 'Altima', 'car_make_id' => $make->id]);
        CarModel::create(['name' => 'Sunny', 'car_make_id' => $make->id]);
        CarModel::create(['name' => 'Patrol', 'car_make_id' => $make->id]);
    }
}
