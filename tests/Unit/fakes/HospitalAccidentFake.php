<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\fakes;


use App\HospitalAccident;

class HospitalAccidentFake implements Fake
{
    public static function make(array $params = [], array $additionalParams = [])
    {
        return factory(HospitalAccident::class)->make($params);
    }
}
