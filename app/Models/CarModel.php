<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    public function make()
    {
        return $this->belongsTo(CarMake::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
