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

namespace medcenter24\mcCore\App\Listeners\Accident\UpdateAccidentStatus;

use medcenter24\mcCore\App\Events\Accident\Caseable\HospitalAccidentUpdatedEvent;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Services\CaseServices\AccidentStatusVisor\HospitalCaseVisorService;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;

class OnHospitalAccidentUpdated
{
    use ServiceLocatorTrait;

    /**
     * @param HospitalAccidentUpdatedEvent $event
     * @throws InconsistentDataException
     */
    public function handle(HospitalAccidentUpdatedEvent $event): void
    {
        $this->getHospitalCaseStatusVisorService()
            ->applyChanges($event->getHospitalAccident(), $event->getPreviousHospitalAccident());
    }

    private function getHospitalCaseStatusVisorService(): HospitalCaseVisorService
    {
        return $this->getServiceLocator()->get(HospitalCaseVisorService::class);
    }
}
