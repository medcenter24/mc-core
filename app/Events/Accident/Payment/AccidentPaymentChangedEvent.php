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
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */
declare(strict_types=1);

namespace medcenter24\mcCore\App\Events;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Payment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccidentPaymentChangedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private Accident $accident;
    private Payment $payment;
    private ?Payment $oldPayment;

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
