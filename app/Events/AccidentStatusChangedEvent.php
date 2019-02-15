<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Events;

use App\AccidentAbstract;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AccidentStatusChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var AccidentAbstract
     */
    private $accident;
    /**
     * @var string
     */
    private $commentary;

    /**
     * Create a new event instance.
     *
     * @param AccidentAbstract $accident
     * @param string $commentary
     */
    public function __construct(AccidentAbstract $accident, $commentary = '')
    {
        $this->accident = $accident;
        $this->commentary = $commentary;
    }

    /**
     * @return AccidentAbstract
     */
    public function getAccident(): AccidentAbstract
    {
        return $this->accident;
    }

    /**
     * @return string
     */
    public function getCommentary(): string
    {
        return $this->commentary;
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
