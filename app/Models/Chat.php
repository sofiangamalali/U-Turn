<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function firstMessage()
    {
        return $this->hasOne(Message::class)->orderBy('sent_at', 'asc');
    }
    public function getUnreadCountsAttribute()
    {
        return $this->messages()
            ->where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->count();
    }

    public function getParticipantsAttribute()
    {
        return User::whereIn('id', [
            $this->firstMessage->sender_id,
            $this->firstMessage->receiver_id
        ])->get();
    }

    public function scopeForUser(Builder $query, $userId)
    {
        return $query->whereHas('messages', function ($q) use ($userId) {
            $q->where('sender_id', $userId)
                ->orWhere('receiver_id', $userId);
        });
    }

    public function scopeBuying(Builder $query, $userId)
    {
        return $query->whereHas('messages', function ($q) use ($userId) {
            $q->where('sender_id', $userId);
        })->whereHas('listing', function ($q) use ($userId) {
            $q->where('user_id', '!=', $userId);
        });
    }


    public function scopeSelling(Builder $query, $userId)
    {
        return $query->whereHas('listing', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->whereHas('messages', function ($q) use ($userId) {
            $q->where('sender_id', '!=', $userId);
        });
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest('sent_at');
    }
}
