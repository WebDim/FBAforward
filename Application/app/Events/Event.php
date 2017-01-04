<?php

namespace App\Events;

abstract class Event
{
    //
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        $events->listen('mailer.sending', function ($message) {
            //
        });
    }
}
