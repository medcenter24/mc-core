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
        return factory(Accident::class)->create([
            'created_by' => 0,
            'parent_id' => 0,
            'patient_id' => 0,
            'accident_type_id' => 0,
            'accident_status_id' => 0,
            'assistant_id' => 0,
            'assistant_ref_num' => 'FakeAssistantRef',
            'caseable_id' => 0,
            'caseable_type' => '',
            'ref_num' => 'Fake-import-num',
            'title' => 'FakeImport',
            'city_id' => 0,
            'address' => 'FakeAddress',
            'contacts' => 'FakeContacts',
            'symptoms' => 'FakeSymptoms',
        ]);
    }

    /**
     * todo redundant
     * Accident with default values
     * @return Accident
     */
    public static function defaultAccident()
    {
        $accident = self::accident();
        return $accident;
    }

    /**
     * @return DoctorAccident
     */
    public static function doctorAccident()
    {
        return factory(DoctorAccident::class)->create([
            'doctor_id' => 0,
            'city_id' => 0,
            'status' => DoctorAccident::STATUS_NEW,
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
        $doctorAccident = self::doctorAccident();
        return $doctorAccident;
    }
}
