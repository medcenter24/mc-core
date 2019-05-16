<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Events;

use medcenter24\mcCore\App\DoctorAccident;
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
