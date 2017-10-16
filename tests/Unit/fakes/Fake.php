<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\fakes;


interface Fake
{
    /**
     * Returns faked object in memory
     * @param array $params
     * @param array $additionalParams if model has dependencies, we can provide needed params
     *  example: ['city' => ['title' => 'Kiev']] // for model city will be used title Kiev
     * @return mixed
     */
    public static function make(array $params = [], array $additionalParams = []);
}
