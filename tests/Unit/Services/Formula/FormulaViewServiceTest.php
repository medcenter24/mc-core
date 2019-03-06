<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Formula;

use App\Contract\Formula\FormulaBuilder;
use App\Models\Formula\FormulaBuilder as FormulaBuilderModel;
use App\Services\Formula\FormulaViewService;

class FormulaViewServiceTest extends AbstractDataProvider
{
    /**
     * @var FormulaViewService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = new FormulaViewService();
    }

    /**
     * @dataProvider dataProviders
     * @param FormulaBuilder $builder
     * @param string $view
     * @param int $expectedResult
     * @param string $description
     * @throws \Throwable
     */
    public function testView(FormulaBuilder $builder, $view = '', $expectedResult = 0, $description = ''): void
    {
        self::assertEquals($view, $this->service->render($builder), $description);
    }

    /**
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     * @expectedException \App\Models\Formula\Exception\FormulaException
     * @expectedExceptionMessage Divide by zero
     */
    public function testDivideByZero(): void
    {
        $this->service->render( (new FormulaBuilderModel())->divInteger(0) );
    }

    /**
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     * @expectedException \App\Models\Formula\Exception\FormulaException
     * @expectedExceptionMessage Divide by zero
     */
    public function testDivideByZero2(): void
    {
        $this->service->render( (new FormulaBuilderModel())->divInteger(1)->divInteger(0) );
    }
}
