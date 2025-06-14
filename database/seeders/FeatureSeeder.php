<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Feature::create(['name' => 'Front Wheel Drive', 'type' => 'technical']);
        Feature::create(['name' => 'Tiptronic Gears', 'type' => 'technical']);
        Feature::create(['name' => 'Cruise Control', 'type' => 'technical']);
        Feature::create(['name' => 'Front Airbags', 'type' => 'technical']);
        Feature::create(['name' => 'Dual Exhaust', 'type' => 'technical']);

        Feature::create(['name' => 'Bluetooth System', 'type' => 'extra']);
        Feature::create(['name' => 'Spoiler', 'type' => 'extra']);
        Feature::create(['name' => 'Heated Seats', 'type' => 'extra']);
        Feature::create(['name' => 'Premium Paint', 'type' => 'extra']);
        Feature::create(['name' => 'Power Windows', 'type' => 'extra']);
    }
}
