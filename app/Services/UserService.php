<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getUserListings()
    {
        return User::with(['listings.images', 'listings.listable'])
            ->findOrFail(auth()->id())
            ->listings()
            ->latest()
            ->get();
    }


    public function getProfile()
    {
        return User::find(auth()->id());
    }
    public function updateProfile($data)
    {
        $user = User::find(auth()->id());
        $user->update($data);
        return $user;
    }

    public function deleteAccount()
    {
        $user = User::find(auth()->id());
        $user->listings()->delete();
        $user->delete();
    }
}
