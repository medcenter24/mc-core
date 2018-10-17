<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Finance\Cases;


use App\Services\AccidentService;
use App\Services\CaseServices\CaseFinanceService;
use App\Services\Formula\FormulaService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Unit\fakes\AccidentFake;

class CaseFinanceServiceTest extends TestCase
{
    use DatabaseMigrations;

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

        $accidentServiceMock = $this->prophesize(AccidentService::class);
        /** @var AccidentService $accidentService */
        $accidentService = $accidentServiceMock->reveal();

        $this->financeService = new CaseFinanceService($formulaService, $accidentService);
    }

    /**
     * Case without anything should return 0 for all valuable variables
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testEmptyCase()
    {
        $accident = AccidentFake::makeDoctorAccident();
        self::assertEquals(0, $this->financeService->calculateToDoctorPayment($accident), 'Doctors payment is correct');
//        self::assertEquals(0, $this->financeService->calculateIncome($accident), 'Income is correct');
        // self::assertEquals(0, $this->financeService->calculateAssistantPayment($accident), 'Assistants payment is correct');
    }
}
