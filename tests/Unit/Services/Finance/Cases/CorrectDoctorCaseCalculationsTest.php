<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Finance\Cases;


use App\Services\AccidentService;
use App\Services\CaseServices\CaseFinanceService;
use App\Services\FinanceConditionService;
use App\Services\Formula\FormulaService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Prophecy\Argument;
use Tests\TestCase;
use Tests\Unit\fakes\AccidentFake;

/**
 * Checking that service calculates everything well
 *
 * Class CorrectDoctorCaseCalculationsTest
 * @package Tests\Unit\Services\Finance\Cases
 */
class CorrectDoctorCaseCalculationsTest extends TestCase
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

        $financeConditionServiceMock = $this->prophesize(FinanceConditionService::class);

        $financeConditionServiceMock->findConditions(Argument::any())->willReturn(collect([]));

        /** @var FinanceConditionService $financeConditionService */
        $financeConditionService = $financeConditionServiceMock->reveal();

        $this->financeService = new CaseFinanceService($formulaService, $accidentService, $financeConditionService);
    }

    public function dataProviders(){
        return [
            [5, 5],
            // todo implement discount for the assistant on the payment
        ];
    }

    /**
     * @dataProvider dataProviders
     * @throws \App\Exceptions\InconsistentDataException
     */
    public function testCalculation($assistantPayment, $income)
    {
        $accident = AccidentFake::makeDoctorAccident([
            'assistant_payment' => $assistantPayment,
        ]);
        self::assertEquals($assistantPayment, $this->financeService->calculateFromAssistantPayment($accident), 'Assistants payment is correct');
        self::assertEquals(0, $this->financeService->calculateToDoctorPayment($accident), 'Doctors payment is correct');
        self::assertEquals($income, $this->financeService->calculateIncome($accident), 'Income is correct');
    }
}
