<?php
/**
 * Copyright (c) 2019.
 *
 * Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Events;


use App\Accident;
use App\Payment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class AccidentPaymentChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Accident
     */
    private $accident;

    /**
     * @var Payment
     */
    private $payment;

    /**
     * @var Payment
     */
    private $oldPayment;

    public function __construct(Accident $accident, Payment $payment, Payment $oldPayment = null)
    {
        $this->accident = $accident;
        $this->payment = $payment;
        $this->oldPayment = $oldPayment;
    }

    /**
     * @return Accident
     */
    public function getAccident(): Accident
    {
        return $this->accident;
    }

    /**
     * @return Payment|null
     */
    public function getOldPayment(): ?Payment
    {
        return $this->oldPayment;
    }

    /**
     * @return Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('channel-name');
    }
}
