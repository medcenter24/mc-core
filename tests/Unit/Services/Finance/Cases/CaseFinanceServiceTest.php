<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Finance\Cases;


use App\Accident;
use App\DoctorAccident;
use App\HospitalAccident;
use App\Models\Formula\FormulaBuilder;
use App\Payment;
use App\Services\AccidentService;
use App\Services\CaseServices\CaseFinanceService;
use App\Services\FinanceConditionService;
use App\Services\FormulaService;
use Prophecy\Argument;
use Tests\TestCase;

class CaseFinanceServiceTest extends TestCase
{
    /**
     * @var CaseFinanceService
     */
    private $financeService;

    public function setUp()
    {
        parent::setUp();

        $formulaBuilderMock = $this->prophesize(FormulaBuilder::class);
        $formulaBuilder = $formulaBuilderMock->reveal();

        $formulaServiceMock = $this->prophesize(FormulaService::class);
        $formulaServiceMock->createFormula()->willReturn($formulaBuilder);
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

    private function getAccidentMock()
    {
        return $this->prophesize(Accident::class);
    }

    private function getDoctorAccidentMock()
    {
        $accidentMock = $this->getAccidentMock();
        $accidentMock->getAttribute(Argument::type('string'))->will(function ($args) {
            $value = null;
            if ($args['0'] == 'caseable_type') {
                $value = DoctorAccident::class;
            }
            return $value;
        });
        return $accidentMock;
    }

    private function getHospitalAccidentMock()
    {
        $accidentMock = $this->getAccidentMock();
        $accidentMock->getAttribute(Argument::type('string'))->will(function ($args) {
            $value = null;
            if ($args['0'] == 'caseable_type') {
                $value = HospitalAccident::class;
            }
            return $value;
        });
        return $accidentMock;
    }

    /**
     * Case without anything should return 0 for all valuable variables
     * @throws \App\Exceptions\InconsistentDataException
     */
    public function testEmptyDoctorCase()
    {
        /** @var Accident $accident */
        $accident = $this->getDoctorAccidentMock()->reveal();
        // how much do we need to pay to the doctor?
        self::assertEquals(0, $this->financeService->calculateToDoctorPayment($accident), 'Doctors payment is correct');
        // how much has assistant paid us?
        self::assertEquals(0, $this->financeService->calculateFromAssistantPayment($accident), 'Assistants payment is correct');
        // Income from the accident
        self::assertEquals(0, $this->financeService->calculateIncome($accident), 'Income is correct');
    }

    /**
     * Case without anything should return 0 for all valuable variables
     * @throws \App\Exceptions\InconsistentDataException
     */
    public function testEmptyHospitalCase()
    {
        /** @var Accident $accident */
        $accident = $this->getHospitalAccidentMock()->reveal();

        self::assertEquals(0, $this->financeService->calculateToHospitalPayment($accident), 'Payment is correct');
        self::assertEquals(0, $this->financeService->calculateFromAssistantPayment($accident), 'Assistants payment is correct');
        self::assertEquals(0, $this->financeService->calculateIncome($accident), 'Income is correct');
    }

    /**
     * @expectedException \App\Exceptions\InconsistentDataException
     * @expectedExceptionMessage Hospital Case only
     */
    public function testWrongHospitalCaseException()
    {
        /** @var Accident $accident */
        $accident = $this->getDoctorAccidentMock()->reveal();
        $this->financeService->calculateToHospitalPayment($accident);
    }

    /**
     * @expectedException \App\Exceptions\InconsistentDataException
     * @expectedExceptionMessage DoctorAccident only
     */
    public function testWrongDoctorCaseException()
    {
        /** @var Accident $accident */
        $accident = $this->getHospitalAccidentMock()->reveal();
        $this->financeService->calculateToDoctorPayment($accident);
    }

    /**
     * When the payment already stored - Doesn't need to be calculated again
     */
    public function testCalculateFromAssistantStoredPayment()
    {
        $accidentMock = $this->getAccidentMock();
        $paymentMock = $this->prophesize(Payment::class);
        $accidentMock->getAttribute(Argument::type('string'))->will(function($args) use($paymentMock) {
            if ($args[0] == 'paymentFromAssistant') {
                $paymentMock->getAttribute(Argument::type('string'))->willReturn(10);
                return $paymentMock->reveal();
            }
        });
        $accident = $accidentMock->reveal();

        self::assertEquals(10, $this->financeService->calculateFromAssistantPayment($accident));
    }

    /**
     * This is new payment - needs to be re-calculated
     */
    public function testCalculateFromAssistantNewPayment()
    {
        $accidentMock = $this->getAccidentMock();
        /** @var Accident $accident */
        $accident = $accidentMock->reveal();

        self::assertEquals(0, $this->financeService->calculateFromAssistantPayment($accident));
    }


    // todo this test will use tests from the other methods, we need to test them first
    public function testCalculateIncome()
    {

    }

}
