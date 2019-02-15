<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Events;

use App\DatePeriod;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class DatePeriodChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var DatePeriod
     */
    private $datePeriod;

    /**
     * Create a new event instance.
     *
     * @param $datePeriod
     * @return void
     */
    public function __construct(DatePeriod $datePeriod)
    {
        $this->datePeriod = $datePeriod;
    }

    /**
     * @return DatePeriod
     */
    public function getDatePeriod()
    {
        return $this->datePeriod;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
