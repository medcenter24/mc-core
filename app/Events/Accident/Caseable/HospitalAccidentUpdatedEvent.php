<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */
declare(strict_types=1);

namespace medcenter24\mcCore\App\Events\Accident\Caseable;

use medcenter24\mcCore\App\Entity\AccidentAbstract;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class HospitalAccidentUpdatedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Data that were before updated
     * @var HospitalAccident
     */
    private ?HospitalAccident $previousHospitalAccident;

    /**
     * Current stored model
     * @var HospitalAccident
     */
    private HospitalAccident $hospitalAccident;

    /**
     * Commentary to the operation
     * @var string
     */
    private string $commentary;

    /**
     * Create a new event instance.
     *
     * HospitalAccidentUpdatedEvent constructor.
     * @param AccidentAbstract|HospitalAccident $hospitalAccident
     * @param AccidentAbstract|HospitalAccident $previousHospitalAccident
     * @param string $commentary
     */
    public function __construct(
        AccidentAbstract $hospitalAccident,
        AccidentAbstract $previousHospitalAccident = null,
        $commentary = '')
    {
        $this->previousHospitalAccident = $previousHospitalAccident;
        $this->hospitalAccident = $hospitalAccident;
        $this->commentary = $commentary;
    }

    /**
     * @return HospitalAccident
     */
    public function getPreviousHospitalAccident(): ?HospitalAccident
    {
        return $this->previousHospitalAccident;
    }

    /**
     * @return HospitalAccident
     */
    public function getHospitalAccident(): HospitalAccident
    {
        return $this->hospitalAccident;
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
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
