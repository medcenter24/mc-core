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
