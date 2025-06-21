<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageFeature extends Model
{
    protected $fillable = [
        'package_id',
        'key',
        'label',
        'price',
        'duration_days',
        'is_free',
        'order',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
