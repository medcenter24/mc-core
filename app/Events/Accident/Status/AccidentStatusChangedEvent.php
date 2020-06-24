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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Events\Accident\Status;

use medcenter24\mcCore\App\Entity\AccidentAbstract;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class AccidentStatusChangedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private AccidentAbstract $accident;
    private string $commentary;

    /**
     * Create a new event instance.
     *
     * @param AccidentAbstract $accident
     * @param string $commentary
     */
    public function __construct(AccidentAbstract $accident, string $commentary = '')
    {
        $this->accident = $accident;
        $this->commentary = $commentary;
    }

    public function getAccident(): AccidentAbstract
    {
        return $this->accident;
    }

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
