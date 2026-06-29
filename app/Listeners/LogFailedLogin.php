<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Failed;
use App\Models\LoginActivity;

class LogFailedLogin
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
    public function handle(Failed $event): void
    {
        LoginActivity::create([
            'user_id' => null,
            'email' => request('email'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'event' => 'failed_login',
            'logged_at' => now(),
        ]);
    }
}
