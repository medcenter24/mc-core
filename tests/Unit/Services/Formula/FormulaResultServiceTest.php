<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Formula;

use App\Models\Formula\FormulaBuilder;
use App\Services\Formula\FormulaResultService;

class FormulaResultServiceTest extends AbstractDataProvider
{
    /**
     * @var FormulaResultService
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new FormulaResultService();
    }

    /**
     * @dataProvider dataProviders
     * @param FormulaBuilder $builder
     * @param string $view
     * @param int $expectedResult
     * @param string $description
     * @throws \Throwable
     */
    public function testResult(FormulaBuilder $builder, $view = '', $expectedResult = 0, $description = ''): void
    {
        self::assertEquals($expectedResult, $this->service->calculate($builder), $description);
    }
}
