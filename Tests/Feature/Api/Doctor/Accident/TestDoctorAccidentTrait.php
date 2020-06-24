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

namespace medcenter24\mcCore\Tests\Feature\Api\Doctor\Accident;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\App\Services\Entity\DoctorAccidentService;
use medcenter24\mcCore\Tests\Feature\Api\Doctor\TestTraitDoctorApi;

trait TestDoctorAccidentTrait
{
    use TestTraitDoctorApi;

    protected function createAccidentForDoc(): Accident
    {
        /** @var DoctorAccident $doctorAccident */
        $doctorAccident = factory(DoctorAccident::class)->create([
            DoctorAccidentService::FIELD_DOCTOR_ID => $this->getCurrentDoctor()->getKey(),
        ]);

        /** @var AccidentStatusService $accidentStatusService */
        $accidentStatusService = $this->getServiceLocator()->get(AccidentStatusService::class);
        $status = $accidentStatusService->getDoctorAssignedStatus();

        /** @var Accident $accident */
        $accident = factory(Accident::class)->create([
            AccidentService::FIELD_CASEABLE_TYPE => DoctorAccident::class,
            AccidentService::FIELD_CASEABLE_ID => $doctorAccident->getKey(),
        ]);

        /** default state on the create will be used */
        $accident->setAttribute(AccidentService::FIELD_ACCIDENT_STATUS_ID, $status->getKey());
        $accident->save();

        return $accident;
    }
}
