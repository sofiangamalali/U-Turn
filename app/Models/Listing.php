<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImages;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listing extends Model
{
    use HasImages, SoftDeletes;

    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listable()
    {
        return $this->morphTo();
    }
    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function getFirstImageAttribute()
    {
        return $this->images()->first();
    }
}
