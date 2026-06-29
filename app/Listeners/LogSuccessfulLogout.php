<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Logout;
use App\Models\LoginActivity;

class LogSuccessfulLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        LoginActivity::create([
            'user_id' => $event->user?->id,
            'email' => $event->user?->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'event' => 'logout',
            'logged_at' => now(),
        ]);
    }
}
