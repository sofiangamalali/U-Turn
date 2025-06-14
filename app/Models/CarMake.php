<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarMake extends Model
{
    public function models()
    {
        return $this->hasMany(CarModel::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
