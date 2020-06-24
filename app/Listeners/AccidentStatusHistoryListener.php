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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Listeners;

use medcenter24\mcCore\App\Events\Accident\Status\AccidentStatusChangedEvent;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusHistoryService;

class AccidentStatusHistoryListener
{
    use ServiceLocatorTrait;

    /**
     * Handle the event.
     *
     * @param  AccidentStatusChangedEvent  $event
     * @return void
     */
    public function handle(AccidentStatusChangedEvent $event): void
    {
        if ($event->getAccident()->getAttribute(AccidentService::FIELD_ACCIDENT_STATUS_ID)) {
            $this->getHistoryService()->create([
                'user_id' => auth()->guest() ? 0 : auth()->user()->id,
                'accident_status_id' => $event->getAccident()
                    ->getAttribute(AccidentService::FIELD_ACCIDENT_STATUS_ID),
                'historyable_id' => $event->getAccident()->id,
                'historyable_type' => get_class($event->getAccident()),
                'commentary' => $event->getCommentary(),
            ]);
        }
    }

    private function getHistoryService(): AccidentStatusHistoryService
    {
        return $this->getServiceLocator()->get(AccidentStatusHistoryService::class);
    }

}
