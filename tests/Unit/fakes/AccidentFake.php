<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\fakes;


use App\Accident;
use App\DoctorAccident;
use App\HospitalAccident;

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
        return self::make($params, $additionalParams);
    }
}
