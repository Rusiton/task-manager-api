<?php

namespace App\Listeners;

use App\Events\TestEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogTestEvent
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
    public function handle(TestEvent $event): void
    {
        Log::info('TestEvent recibido en el backend', [
            'message' => $event->message,
            'timestamp' => now()
        ]);
    }
}
