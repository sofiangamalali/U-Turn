<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'is_active',
    ];

    public function perAdFeatures()
    {
        return $this->hasMany(PerAdFeature::class);
    }

    public function subscriptionFeatures()
    {
        return $this->hasMany(SubscriptionFeature::class);
    }


}
