<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\fakes;


use App\Services\AccidentService;

class AccidentServiceFake implements Fake
{
    public static function make(array $params = [], array $additionalParams = [])
    {
        return factory(AccidentService::class)->make();
    }
}
