<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Patient;

class PatientService
{
    /**
     * Create or find a patient according to the patients data
     * @param array $patientData
     * @return bool|Patient
     */
    public function findOrCreate(array $patientData = [])
    {
        $patient = false;
        if (count($patientData)) {
            if (!$patientData['birthday']) {
                $patientData['birthday'] = null;
            }

            $patient = null;
            if (!$patientData['id']) {
                if ($patientData['name']) {
                    $patient = Patient::create($patientData);
                }
            } else {
                $patient = Patient::findOrFail($patientData['id']);
            }
        }

        return $patient;
    }
}
