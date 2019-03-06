<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\fakes;


use App\Hospital;

class HospitalFake implements Fake
{
    public static function make(array $params = [], array $additionalParams = [])
    {
        $hospital = factory(Hospital::class)->make($params);
        return $hospital;
    }
}
