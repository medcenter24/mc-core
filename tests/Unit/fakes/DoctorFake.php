<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\fakes;


use App\Doctor;

class DoctorFake implements Fake
{
    public static function make(array $params = [], array $additionalParams = [])
    {
        $doctor = factory(Doctor::class)->make($params);

        $additionalParams = array_merge(['city' => []], $additionalParams);
        $doctor->city = CityFake::make($additionalParams['city']);
        return $doctor;
    }
}
