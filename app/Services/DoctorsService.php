<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Accident;
use App\Doctor;
use App\DoctorAccident;

class DoctorsService
{

    /**
     * @param Doctor $doctor
     * @param Accident $accident
     *
     * @return bool
     */
    public function hasAccess(Doctor $doctor, Accident $accident)
    {
        return $accident->caseable instanceof DoctorAccident && $accident->caseable->doctor_id == $doctor->id;
    }
}
