<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voice extends Model
{
    protected $fillable = ['file_path'];

    public function voiceable()
    {
        return $this->morphTo();
    }
}
