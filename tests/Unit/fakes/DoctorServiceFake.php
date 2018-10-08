<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\fakes;


use App\DoctorService;

class DoctorServiceFake implements Fake
{
    public static function make(array $params = [], array $additionalParams = [])
    {
        return factory(DoctorService::class)->create();
    }
}
