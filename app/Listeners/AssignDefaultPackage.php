<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\Package;
use App\Models\Subscription;

class AssignDefaultPackage
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        Subscription::create([
            'user_id' => $event->user->id,
            'package_id' => Package::where('type', 'pay_per_ad')->first()->id,
            'started_at' => now(),
            'expires_at' => null,
            'is_active' => true,
        ]);

    }
}
