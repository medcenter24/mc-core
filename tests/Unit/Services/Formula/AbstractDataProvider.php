<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Formula;


use App\Models\Formula\FormulaBuilder;
use Tests\TestCase;

class AbstractDataProvider extends TestCase
{
    public function dataProviders()
    {
        return [
            [
                new FormulaBuilder(),
                '',
                0,
            ],
        ];
    }
}