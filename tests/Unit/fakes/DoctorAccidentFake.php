<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\fakes;


use App\DoctorAccident;

class DoctorAccidentFake implements Fake
{
    public static function make(array $params = [], array $additionalParams = [])
    {
        $doctorAccident = factory(DoctorAccident::class)->make($params);

        $additionalParams = array_merge(['city' => []], $additionalParams);
        $doctorAccident->city = CityFake::make($additionalParams['city']);
        return $doctorAccident;
    }
}
