<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Formula;

use App\Models\Formula\FormulaBuilderInterface;
use App\Services\Formula\FormulaResultService;

class FormulaResultServiceTest extends AbstractDataProvider
{
    /**
     * @var FormulaResultService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = new FormulaResultService();
    }

    /**
     * @dataProvider dataProviders
     * @param FormulaBuilderInterface $builder
     * @param string $view
     * @param int $expectedResult
     * @param string $description
     * @throws \Throwable
     */
    public function testResult(FormulaBuilderInterface $builder, $view = '', $expectedResult = 0, $description = '')
    {
        self::assertEquals($expectedResult, $this->service->calculate($builder), $description);
    }
}
