<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\fakes;


use App\DatePeriodInterpretation;

class DatePeriodInterpretationFake implements Fake
{
    public static function make(array $params = [], array $additionalParams = [])
    {
        return factory(DatePeriodInterpretation::class)->make($params);
    }
}
