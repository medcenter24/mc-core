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
