<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Formula;

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
        $this->service = new FormulaViewService();
    }

    /**
     * @dataProvider dataProviders
     * @param FormulaBuilderInterface $builder
     * @param string $view
     * @param int $expectedResult
     * @throws \Throwable
     */
    public function testView(FormulaBuilderInterface $builder, $view = '', $expectedResult = 0)
    {
        self::assertEquals($view, $this->service->render($builder), 'Correct view');
    }
}
