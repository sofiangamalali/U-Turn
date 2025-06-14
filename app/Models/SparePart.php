<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{
    protected $guarded = [];


    public function make()
    {
        return $this->belongsTo(CarMake::class, 'car_make_id');
    }
    public function model()
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }
    public function listing()
    {
        return $this->morphOne(Listing::class, 'listable');
    }
}
