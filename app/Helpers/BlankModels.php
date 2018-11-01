<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Helpers;


use App\Accident;
use App\DoctorAccident;

class BlankModels
{
    /**
     * New blank accident
     * @return Accident
     */
    public static function accident()
    {
        return Accident::create([
            'created_by' => 0,
            'parent_id' => 0,
            'patient_id' => 0,
            'accident_type_id' => 0,
            'accident_status_id' => 0,
            'assistant_id' => 0,
            'assistant_ref_num' => '',
            'caseable_id' => 0,
            'caseable_type' => '',
            'ref_num' => '',
            'title' => '',
            'city_id' => 0,
            'address' => '',
            'contacts' => '',
            'symptoms' => '',
            'handlingTime' => ''
        ]);
    }

    /**
     * @return DoctorAccident
     */
    public static function doctorAccident()
    {
        return DoctorAccident::create([
            'doctor_id' => 0,
            'city_id' => 0,
            'recommendation' => '',
            'investigation' => '',
        ]);
    }
}
