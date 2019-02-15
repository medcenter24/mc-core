<?php
/**
 * Copyright (c) 2019.
 *
 * Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Listeners;


use App\Events\AccidentPaymentChangedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccidentPaymentListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AccidentPaymentChangedEvent  $event
     * @return void
     */
    public function handle(AccidentPaymentChangedEvent $event): void
    {
        \Log::notice('Payment were changed', [
            $event->getAccident(),
            $event->getPayment(),
            $event->getOldPayment(),
        ]);
    }
}
