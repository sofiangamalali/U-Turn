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

    public function features()
    {
        return $this->hasMany(PackageFeature::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            Subscription::class,
            'package_id',    // Foreign key on subscriptions table
            'id',            // Foreign key on users table
            'id',            // Local key on packages table
            'user_id'        // Local key on subscriptions table
        );
    }

}
