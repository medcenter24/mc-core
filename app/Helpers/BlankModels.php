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

namespace medcenter24\mcCore\App\Helpers;


use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\DoctorAccident;

/**
 * Class BlankModels
 * @package medcenter24\mcCore\App\Helpers
 * @deprecated not the best way to do it, try to use Services
 * maybe AccidentService::getFormattedAccidentData()
 */
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
            'recommendation' => '',
            'investigation' => '',
        ]);
    }
}
