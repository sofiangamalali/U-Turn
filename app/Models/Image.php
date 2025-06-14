<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['file_path', 'caption'];

    public function imageable()
    {
        return $this->morphTo();
    }
}
