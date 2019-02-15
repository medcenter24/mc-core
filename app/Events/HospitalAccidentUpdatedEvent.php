<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Events;


use App\HospitalAccident;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class HospitalAccidentUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Data that were before updated
     * @var HospitalAccident
     */
    private $previousData;

    /**
     * Current stored model
     * @var HospitalAccident
     */
    private $hospitalAccident;

    /**
     * Commentary to the operation
     * @var string
     */
    private $commentary = '';

    /**
     * Create a new event instance.
     *
     * HospitalAccidentUpdatedEvent constructor.
     * @param HospitalAccident|null $previousData
     * @param HospitalAccident $hospitalAccident
     * @param string $commentary
     */
    public function __construct(HospitalAccident $previousData = null, HospitalAccident $hospitalAccident, $commentary = '')
    {
        $this->previousData = $previousData;
        $this->hospitalAccident = $hospitalAccident;
        $this->commentary = $commentary;
    }

    /**
     * @return HospitalAccident
     */
    public function getPreviousData()
    {
        return $this->previousData;
    }

    /**
     * @return HospitalAccident
     */
    public function getDoctorAccident()
    {
        return $this->hospitalAccident;
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
