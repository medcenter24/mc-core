<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Finance;


use App\Accident;
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
        $formulaService = $this->prophesize(FormulaService::class);
        $this->financeService = new CaseFinanceService($formulaService->reveal());
    }

    /**
     * Case without anything should return 0 for all valuable variables
     */
    public function testEmptyCase()
    {
        $accident = AccidentFake::make();
        self::assertEquals(0, $this->financeService->calculateIncome($accident), 'Income is correct');
        self::assertEquals(0, $this->financeService->calculateDoctorPayment($accident), 'Doctor payment is correct');
        self::assertEquals(0, $this->financeService->calculateAssistantPayment($accident), 'Assistant payment is correct');
    }
}
