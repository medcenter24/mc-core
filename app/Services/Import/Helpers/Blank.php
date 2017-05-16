<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Import\Helpers;


use App\Accident;
use App\DoctorAccident;

class Blank
{
    /**
     * @return Accident
     */
    public static function blankAccident()
    {
        return factory(Accident::class)->create([
            'created_by' => 0,
            'parent_id' => 0,
            'patient_id' => 0,
            'accident_type_id' => 0,
            'accident_status_id' => 0,
            'assistant_id' => 0,
            'assistant_ref_num' => 'FakeAssistantRef',
            'caseable_id' => 0,
            'caseable_type' => \App\DoctorAccident::class,
            'ref_num' => 'Fake-import-num',
            'title' => 'FakeImport',
            'city_id' => 0,
            'address' => 'FakeAddress',
            'contacts' => 'FakeContacts',
            'symptoms' => 'FakeSymptoms',
        ]);
    }

    /**
     * @return Accident
     */
    public static function defaultAccident()
    {
        $accident = self::blankAccident();
        return $accident;
    }

    /**
     * @return DoctorAccident
     */
    public static function blankDoctorAccident()
    {
        return factory(DoctorAccident::class)->create([
            'doctor_id' => 0,
            'city_id' => 0,
            'status' => \App\DoctorAccident::STATUS_CLOSED,
            'diagnose' => 'FakeDiagnose',
            'investigation' => 'FakeInvestigation',
            'accident_status_id' => 0,
        ]);
    }

    /**
     * @return DoctorAccident
     */
    public static function defaultDoctorAccident()
    {
        $doctorAccident = self::blankDoctorAccident();
        return $doctorAccident;
    }
}
