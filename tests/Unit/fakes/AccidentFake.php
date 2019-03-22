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

namespace Tests\Unit\fakes;


use App\Accident;
use App\DoctorAccident;
use App\HospitalAccident;

// TODO Delete this

/**
 * Class AccidentFake
 * @package Tests\Unit\fakes
 * @deprecated You need to use prophesize instead! do not use storage anymore!
 */
class AccidentFake implements Fake
{
    public static function make(array $params = [], array $additionalParams = [])
    {
        $accident = factory(Accident::class)->make($params);

        $defaults = [
            'assistant' => [],
        ];
        if (isset($params['caseable_type']) && $params['caseable_type'] == DoctorAccident::class) {
            $defaults['doctorAccident'] = [];
            $defaults['doctor'] = [];
            $additionalParams = array_merge($defaults, $additionalParams);
            $accident->caseable = DoctorAccidentFake::make($additionalParams['doctorAccident']);
            $accident->caseable->doctor = DoctorFake::make($additionalParams['doctor']);
        } else {
            $defaults['hospitalAccident'] = [];
            $additionalParams = array_merge($defaults, $additionalParams);
            $accident->caseable = HospitalAccidentFake::make($additionalParams['hospitalAccident'], $additionalParams);
        }

        $accident->assistant = AssistantFake::make($additionalParams['assistant']);
        return $accident;
    }

    public static function makeDoctorAccident(array $params = [], array $additionalParams = [])
    {
        $params['caseable_type'] = DoctorAccident::class;
        if (!isset($additionalParams['doctorAccident'])) {
            $additionalParams['doctorAccident'] = [];
        }
        if (!isset($additionalParams['doctor'])) {
            $additionalParams['doctor'] = [];
        }
        return self::make($params, $additionalParams);
    }

    public static function makeHospitalAccident(array $params = [], array $additionalParams = [])
    {
        $params['caseable_type'] = HospitalAccident::class;
        if (!isset($additionalParams['hospitalAccident'])) {
            $additionalParams['hospitalAccident'] = [];
        }
        if (!isset($additionalParams['hospital'])) {
            $additionalParams['hospital'] = [];
        }
        return self::make($params, $additionalParams);
    }
}
