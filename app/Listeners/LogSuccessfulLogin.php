<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Models\LoginActivity;

class LogSuccessfulLogin
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

    public function handle(Login $event): void
    {
        LoginActivity::create([
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'event' => 'login',
            'logged_at' => now(),
        ]);
    }

}
