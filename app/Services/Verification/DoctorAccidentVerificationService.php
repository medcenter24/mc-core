<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Verification;


use App\Accident;
use App\City;
use App\DoctorAccident;
use App\Patient;

/**
 * Generate data which should be sent based on the DoctorAccident model
 *
 *
 * Class DoctorAccidentVerificationService
 * @package App\Services\Verification
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
