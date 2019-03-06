<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\fakes;


use App\City;

class CityFake implements Fake
{
    public static function make(array $params = [], array $additionalParams = [])
    {
        return factory(City::class)->create($params);
    }
}
