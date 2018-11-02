<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Formula;

use App\Models\Formula\FormulaBuilder;
use App\Models\Formula\FormulaBuilderInterface;
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
     * @param FormulaBuilderInterface $builder
     * @param string $view
     * @param int $expectedResult
     * @param string $description
     * @throws \Throwable
     */
    public function testView(FormulaBuilderInterface $builder, $view = '', $expectedResult = 0, $description = '')
    {
        self::assertEquals($view, $this->service->render($builder), $description);
    }

    /**
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     * @expectedException \App\Models\Formula\Exception\FormulaException
     * @expectedExceptionMessage Divide by zero
     */
    public function testDivideByZero()
    {
        $this->service->render( (new FormulaBuilder())->divInteger(0) );
    }

    /**
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     * @expectedException \App\Models\Formula\Exception\FormulaException
     * @expectedExceptionMessage Divide by zero
     */
    public function testDivideByZero2()
    {
        $this->service->render( (new FormulaBuilder())->divInteger(1)->divInteger(0) );
    }
}
