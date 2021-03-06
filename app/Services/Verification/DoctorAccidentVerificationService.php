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

namespace medcenter24\mcCore\App\Services\Verification;


use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\City;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Patient;

/**
 * Generate data which should be sent based on the DoctorAccident model
 *
 *
 * Class DoctorAccidentVerificationService
 * @package medcenter24\mcCore\App\Services\Verification
 */
class DoctorAccidentVerificationService
{
    /**
     * @param DoctorAccident $doctorAccident
     * @return string
     */
    public function getMessage(DoctorAccident $doctorAccident)
    {
        /** @var Accident $accident */
        $accident = $doctorAccident->accident;
        /** @var City $city */
        $city = $accident->city;
        /** @var string $address */
        $address = $accident->address;
        /** @var Patient $patien */
        $patien = $accident->patient;
        $contacts = $accident->contacts;
        $symptoms = $accident->symptoms;
        
    }
}
