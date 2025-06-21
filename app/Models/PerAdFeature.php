<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerAdFeature extends Model
{
    protected $fillable = [
        'package_id', 'label', 'price', 'duration_days',
        'is_free', 'order', 'level'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}