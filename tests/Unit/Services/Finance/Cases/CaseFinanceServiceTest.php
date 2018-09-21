<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Finance\Cases;


use App\Services\CaseServices\CaseFinanceService;
use App\Services\Formula\FormulaService;
use Tests\TestCase;
use Tests\Unit\fakes\AccidentFake;

class CaseFinanceServiceTest extends TestCase
{
    /**
     * @var CaseFinanceService
     */
    private $financeService;

    public function setUp()
    {
        parent::setUp();
        $formulaServiceMock = $this->prophesize(FormulaService::class);
        /** @var FormulaService $formulaService */
        $formulaService = $formulaServiceMock->reveal();
        $this->financeService = new CaseFinanceService($formulaService);
    }

    /**
     * Case without anything should return 0 for all valuable variables
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testEmptyCase()
    {
        $accident = AccidentFake::make();
        self::assertEquals(0, $this->financeService->calculateIncome($accident), 'Income is correct');
        //self::assertEquals(0, $this->financeService->calculateDoctorPayment($accident), 'Doctors payment is correct');
        //self::assertEquals(0, $this->financeService->calculateAssistantPayment($accident), 'Assistants payment is correct');
    }
}
