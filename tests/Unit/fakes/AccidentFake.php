<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\fakes;


use App\Accident;

class AccidentFake implements Fake
{
    public static function make(array $params = [], array $additionalParams = [])
    {
        $additionalParams = array_merge([
            'assistant' => [],
            'doctorAccident' => [],
            'doctor' => [],
        ], $additionalParams);

        $accident = factory(Accident::class)->make($params);

        $accident->assistant = AssistantFake::make($additionalParams['assistant']);
        $accident->caseable = DoctorAccidentFake::make($additionalParams['doctorAccident']);
        $accident->caseable->doctor = DoctorFake::make($additionalParams['doctor']);
        return $accident;
    }
}
