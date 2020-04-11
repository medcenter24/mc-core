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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\Accident;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\ApiDoctorTrait;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\App\Services\Entity\DoctorAccidentService;

trait DoctorAccidentControllerTrait
{
    use ApiDoctorTrait;

    protected function checkAccess(?Accident $accident): void
    {
        if (!$accident) {
            $this->response->errorNotFound();
        }

        /** @var DoctorAccident $doctorAccident */
        $doctorAccident = $accident->getAttribute('caseable');
        if (!is_a($doctorAccident, DoctorAccident::class)) {
            $this->response->errorNotFound();
        }

        $caseDoctorId = (int) $doctorAccident->getAttribute(DoctorAccidentService::FIELD_DOCTOR_ID);
        if (!$caseDoctorId || $caseDoctorId !== $this->getDoctorId()) {
            $this->response->errorNotFound();
        }

        if (
            !$accident->accidentStatus->type === AccidentStatusService::TYPE_DOCTOR
            || ( !in_array($accident->accidentStatus->title, [
                AccidentStatusService::STATUS_ASSIGNED,
                AccidentStatusService::STATUS_IN_PROGRESS,
            ], false)
            )
        ) {
            $this->response->errorMethodNotAllowed('You cant\'t change this case');
        }
    }
}
