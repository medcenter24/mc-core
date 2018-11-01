<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Formula;

use App\Services\Formula\FormulaResultService;
use Tests\TestCase;

class FormulaResultServiceTest extends AbstractDataProvider
{
    private $service;

    public function setUp()
    {
        $this->service = new FormulaResultService();
    }

    /**
     * @dataProvider dataProviders
     */
    public function testView()
    {

    }
}
