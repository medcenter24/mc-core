<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\fakes;


use App\Assistant;

class AssistantFake implements Fake
{
    public static function make(array $params = [], array $additionalParams = [])
    {
        return factory(Assistant::class)->make($params);
    }
}
