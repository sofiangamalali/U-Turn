<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = ['name', 'type'];

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'feature_vehicle');
    }
}
