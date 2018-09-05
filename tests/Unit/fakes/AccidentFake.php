<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\fakes;


use App\Accident;
use App\DoctorAccident;

class AccidentFake implements Fake
{
    public static function make(array $params = [], array $additionalParams = [])
    {
        $accident = factory(Accident::class)->make($params);

        $defaults = [
            'assistant' => [],
            'doctor' => [],
        ];
        if (isset($params['caseable_type']) && $params['caseable_type'] == DoctorAccident::class) {
            $defaults['doctorAccident'] = [];
            $additionalParams = array_merge($defaults, $additionalParams);
            $accident->caseable = DoctorAccidentFake::make($additionalParams['doctorAccident']);
            $accident->caseable->doctor = DoctorFake::make($additionalParams['doctor']);
        } else {
            $defaults['hospitalAccident'] = [];
            $additionalParams = array_merge($defaults, $additionalParams);
            $accident->caseable = HospitalAccidentFake::make($additionalParams['hospitalAccident']);
        }

        $accident->assistant = AssistantFake::make($additionalParams['assistant']);
        return $accident;
    }
}
