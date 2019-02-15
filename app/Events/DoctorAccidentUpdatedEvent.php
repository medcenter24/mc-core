<?php

namespace App\Events;

use App\DoctorAccident;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class DoctorAccidentUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Data that were before updated
     * @var DoctorAccident
     */
    private $previousData;

    /**
     * Current stored model
     * @var DoctorAccident
     */
    private $doctorAccident;

    /**
     * Commentary to the operation
     * @var string
     */
    private $commentary = '';

    /**
     * Create a new event instance.
     *
     * DoctorAccidentUpdatedEvent constructor.
     * @param DoctorAccident|null $previousData
     * @param DoctorAccident $doctorAccident
     * @param string $commentary
     */
    public function __construct(DoctorAccident $previousData = null, DoctorAccident $doctorAccident, $commentary = '')
    {
        $this->previousData = $previousData;
        $this->doctorAccident = $doctorAccident;
        $this->commentary = $commentary;
    }

    /**
     * @return DoctorAccident
     */
    public function getPreviousData()
    {
        return $this->previousData;
    }

    /**
     * @return DoctorAccident
     */
    public function getDoctorAccident()
    {
        return $this->doctorAccident;
    }

    /**
     * @return string
     */
    public function getCommentary()
    {
        return $this->commentary;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
