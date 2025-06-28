<?php

namespace App\Models;

use App\Traits\HasImages;
use App\Traits\HasVoices;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasImages, HasVoices;
    protected $guarded = [];

    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function getImageAttribute()
    {
        return $this->images->first();
    }

    public function getVoiceAttribute()
    {
        return $this->voices->first();
    }
    public function getIsCurrentUserAttribute(): bool
    {
        return $this->sender_id === auth()->id();
    }
}
