<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImages;

class Listing extends Model
{
    use HasImages;
    protected $guarded = [];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listable()
    {
        return $this->morphTo();
    }
}
