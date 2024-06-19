<?php

namespace App\Listeners;

use App\Events\RegisterUser;
use App\Jobs\SendVerificationEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailVerificationNotification
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
    public function handle(RegisterUser $event): void
    {
        SendVerificationEmail::dispatch($event->user);
    }
}
