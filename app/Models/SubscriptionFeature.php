<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionFeature extends Model
{
    protected $fillable = [
        'package_id',
        'title',
        'max_ads',
        'price',
        'description',
        'duration_days'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}