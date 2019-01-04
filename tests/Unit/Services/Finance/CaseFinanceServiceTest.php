<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services\Finance;


use App\Accident;
use App\Contract\Formula\FormulaBuilder;
use App\DoctorAccident;
use App\HospitalAccident;
use App\Invoice;
use App\Payment;
use App\Services\AccidentService;
use App\Services\CaseServices\CaseFinanceService;
use App\Services\FinanceConditionService;
use App\Services\Formula\FormulaService;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Tests\TestCase;

class CaseFinanceServiceTest extends TestCase
{
    private function getExpectation($key, $list)
    {
        return array_key_exists($key, $list) ? $list[$key] : 0;
    }

    /**
     * @param array $expects
     * [
     *      'formulaServiceMock->createFormula' => 0,
     *      'formulaServiceMock->createFormulaFromConditions' => 0,
     *      'financeConditionServiceMock->findConditions' => 0,
     * ]
     * @return CaseFinanceService
     */
    private function financeService(array $expects = []): CaseFinanceService
    {
        $formulaBuilder = new FormulaBuilderUnit();

        /** @var ObjectProphecy|FormulaService $formulaServiceMock */
        $formulaServiceMock = $this->prophesize(FormulaService::class);
        $formulaServiceMock->createFormula()
            ->shouldBeCalledTimes($this->getExpectation('formulaServiceMock->createFormula', $expects))
            ->willReturn($formulaBuilder);
        $formulaServiceMock->createFormulaFromConditions(Argument::any())
            ->shouldBeCalledTimes($this->getExpectation('formulaServiceMock->createFormulaFromConditions', $expects))
            ->willReturn($formulaBuilder);
        /** @var FormulaService $formulaService */
        $formulaService = $formulaServiceMock->reveal();

        $accidentServiceMock = $this->prophesize(AccidentService::class);
        /** @var AccidentService $accidentService */
        $accidentService = $accidentServiceMock->reveal();

        $financeConditionServiceMock = $this->prophesize(FinanceConditionService::class);
        $financeConditionServiceMock->findConditions(Argument::any())
            ->shouldBeCalledTimes($this->getExpectation('financeConditionServiceMock->findConditions', $expects))
            ->will(function ($args) {
            if (key_exists(DoctorAccident::class, $args[0])) {
                if (!$args[0][DoctorAccident::class]) {
                    return collect([]);
                } else {
                    return collect([1]);
                }
            } elseif (key_exists(HospitalAccident::class, $args[0])) {
                if (!$args[0][HospitalAccident::class]) {
                    return collect([]);
                } else {
                    return collect([1]);
                }
            } else {
                return collect([]);
            }
        });

        /** @var FinanceConditionService $financeConditionService */
        $financeConditionService = $financeConditionServiceMock->reveal();

        return new CaseFinanceService($formulaService, $accidentService, $financeConditionService);
    }

    private function getAccidentMock(): ObjectProphecy
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

    /**
     * @return ObjectProphecy
     */
    private function getHospitalAccidentMock(): ObjectProphecy
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
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testEmptyDoctorCase(): void
    {
        /** @var Accident $accident */
        $accident = $this->getDoctorAccidentMock()->reveal();
        // how much do we need to pay to the doctor?
        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getToDoctorPaymentFormula($accident), 'Doctors payment is correct');
        // how much has assistant paid us?
        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'formulaServiceMock->createFormulaFromConditions' => 0,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getFromAssistantPaymentFormula($accident), 'Assistants payment is correct');
        // Income from the accident
        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 3,
            'formulaServiceMock->createFormulaFromConditions' => 0,
            'financeConditionServiceMock->findConditions' => 2,
        ])->getIncomeFormula($accident), 'Income is correct');
    }

    /**
     * Case without anything should return 0 for all valuable variables
     * @throws \App\Exceptions\InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testEmptyHospitalCase()
    {
        /** @var Accident $accident */
        $accident = $this->getHospitalAccidentMock()->reveal();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getToHospitalPaymentFormula($accident), 'Payment is correct');
        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'formulaServiceMock->createFormulaFromConditions' => 0,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getFromAssistantPaymentFormula($accident), 'Assistants payment is correct');
        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 3,
            'formulaServiceMock->createFormulaFromConditions' => 0,
            'financeConditionServiceMock->findConditions' => 2,
        ])->getIncomeFormula($accident), 'Income is correct');
    }

    /**
     * @expectedException \App\Exceptions\InconsistentDataException
     * @expectedExceptionMessage Hospital Case only
     * @throws \App\Exceptions\InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testWrongHospitalCaseException(): void
    {
        /** @var Accident $accident */
        $accident = $this->getDoctorAccidentMock()->reveal();
        $this->financeService()->getToHospitalPaymentFormula($accident);
    }

    /**
     * @expectedException \App\Exceptions\InconsistentDataException
     * @expectedExceptionMessage DoctorAccident only
     * @throws \App\Exceptions\InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testWrongDoctorCaseException(): void
    {
        /** @var Accident $accident */
        $accident = $this->getHospitalAccidentMock()->reveal();
        $this->financeService()->getToDoctorPaymentFormula($accident);
    }

    /**
     * When the payment already stored (when we had static digit in the DB) - Doesn't need to calculate again
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testStoredFromAssistantPayment(): void
    {
        $paymentMock = $this->prophesize(Payment::class);
        $paymentMock->getAttribute(Argument::type('string'))->willReturn(10);

        $accidentMock = $this->getAccidentMock();
        $accidentMock->getAttribute(Argument::type('string'))->will(function($args) use($paymentMock) {
            if ($args[0] == 'paymentFromAssistant') {
                return $paymentMock->reveal();
            }
        });
        /** @var Accident $accident */
        $accident = $accidentMock->reveal();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
        ])->getFromAssistantPaymentFormula($accident));
    }

    /**
     * This is payment that needs to be calculated according to the conditions
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testCalculateFromAssistantEmptyPayment(): void
    {
        $accidentMock = $this->getAccidentMock();
        /** @var Accident $accident */
        $accident = $accidentMock->reveal();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getFromAssistantPaymentFormula($accident));
    }

    /**
     * This is payment that needs to be calculated according to the conditions
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testCalculateFromAssistantPaymentWithCondition(): void
    {
        $paymentMock = $this->prophesize(Payment::class);
        $paymentMock->getAttribute(Argument::type('string'))->willReturn(987);

        $assistantGuaranteeMock = $this->prophesize(Invoice::class);
        $assistantGuaranteeMock->getAttribute(Argument::type('string'))->will(function($args) use ($paymentMock){
            if ($args[0] == 'payment') {
                return $paymentMock->reveal();
            }
        });

        $accidentMock = $this->getAccidentMock();
        $accidentMock->getAttribute(Argument::type('string'))->will(function($args) use ($assistantGuaranteeMock) {
            if ($args[0] == 'assistantGuarantee') {
                return $assistantGuaranteeMock->reveal();
            }
            if ($args[0] == 'caseable_id') {
                return 5;
            }
        });
        /** @var Accident $accident */
        $accident = $accidentMock->reveal();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
            'formulaServiceMock->createFormulaFromConditions' => 1,
        ])->getFromAssistantPaymentFormula($accident));
    }

    /**
     * @throws \App\Exceptions\InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testCalculateToDoctorPayment(): void
    {
        $accidentMock = $this->getAccidentMock();
        $accidentMock->getAttribute(Argument::type('string'))->will(function($args) {
            if ($args[0] == 'caseable_type') {
                return DoctorAccident::class;
            }
            if ($args[0] == 'caseable_id') {
                return 0;
            }
        });
        /** @var Accident $accident */
        $accident = $accidentMock->reveal();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getToDoctorPaymentFormula($accident));
    }

    /**
     * @throws \App\Exceptions\InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testCalculateToDoctorPaymentWithCondition(): void
    {
        $accidentMock = $this->getAccidentMock();
        $accidentMock->getAttribute(Argument::type('string'))->will(function($args) {
            if ($args[0] == 'caseable_type') {
                return DoctorAccident::class;
            }
            if ($args[0] == 'caseable_id') {
                return 5;
            }
            return null;
        });
        /** @var Accident $accident */
        $accident = $accidentMock->reveal();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
            'formulaServiceMock->createFormulaFromConditions' => 1,
        ])->getToDoctorPaymentFormula($accident));
    }

    /**
     * @throws \App\Exceptions\InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testStoredToDoctorPayment(): void
    {
        $paymentMock = $this->prophesize(Payment::class);
        $paymentMock->getAttribute(Argument::type('string'))->willReturn(10);

        $accidentMock = $this->getAccidentMock();
        $accidentMock->getAttribute(Argument::type('string'))->will(function($args) use($paymentMock) {
            if ($args[0] == 'caseable_type') {
                return DoctorAccident::class;
            }
            if ($args[0] == 'paymentToCaseable') {
                return $paymentMock->reveal();
            }

            return null;
        });
        /** @var Accident $accident */
        $accident = $accidentMock->reveal();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
        ])->getToDoctorPaymentFormula($accident));
    }

    /**
     * @throws \App\Exceptions\InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testCalculateToHospitalPayment(): void
    {
        $accidentMock = $this->getAccidentMock();
        $accidentMock->getAttribute(Argument::type('string'))->will(function($args) {
            if ($args[0] == 'caseable_type') {
                return HospitalAccident::class;
            }
            if ($args[0] == 'caseable_id') {
                return 0;
            }
        });
        /** @var Accident $accident */
        $accident = $accidentMock->reveal();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getToHospitalPaymentFormula($accident));
    }

    /**
     * @throws \App\Exceptions\InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testCalculateToHospitalPaymentWithCondition(): void
    {
        $accidentMock = $this->getAccidentMock();
        $accidentMock->getAttribute(Argument::type('string'))->will(function($args) {
            if ($args[0] == 'caseable_type') {
                return HospitalAccident::class;
            }
            if ($args[0] == 'caseable_id') {
                return 5;
            }
            return null;
        });
        /** @var Accident $accident */
        $accident = $accidentMock->reveal();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
            'formulaServiceMock->createFormulaFromConditions' => 1,
        ])->getToHospitalPaymentFormula($accident));
    }

    /**
     * @throws \App\Exceptions\InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testStoredToHospitalPayment(): void
    {
        $paymentMock = $this->prophesize(Payment::class);
        $paymentMock->getAttribute(Argument::type('string'))->willReturn(10);

        $accidentMock = $this->getAccidentMock();
        $accidentMock->getAttribute(Argument::type('string'))->will(function($args) use($paymentMock) {
            if ($args[0] == 'caseable_type') {
                return HospitalAccident::class;
            }
            if ($args[0] == 'paymentToCaseable') {
                return $paymentMock->reveal();
            }

            return null;
        });
        /** @var Accident $accident */
        $accident = $accidentMock->reveal();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
        ])->getToHospitalPaymentFormula($accident));
    }

    /**
     * Income on the empty accident
     * @throws \App\Exceptions\InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testCalculateIncome(): void
    {
        $accidentMock = $this->getAccidentMock();
        /** @var Accident $accident */
        $accident = $accidentMock->reveal();
        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 2,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getIncomeFormula($accident));
    }

    /**
     * Income on the empty accident
     * @throws \App\Exceptions\InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testStoredIncome(): void
    {
        $paymentMock = $this->prophesize(Payment::class);
        $paymentMock->getAttribute(Argument::type('string'))->willReturn(10);

        $accidentMock = $this->getAccidentMock();
        $accidentMock->getAttribute(Argument::type('string'))->will(function($args) use($paymentMock) {
            if ($args[0] == 'caseable_type') {
                return HospitalAccident::class;
            }
            if ($args[0] == 'getIncomePayment') {
                return $paymentMock->reveal();
            }

            return null;
        });
        /** @var Accident $accident */
        $accident = $accidentMock->reveal();
        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
        ])->getIncomeFormula($accident));
    }

    /**
     * @throws \App\Exceptions\InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function testCalculateIncomeWithCondition()
    {
        $paymentMock = $this->prophesize(Payment::class);
        $paymentMock->getAttribute(Argument::type('string'))->willReturn(10);

        $accidentMock = $this->getAccidentMock();
        $accidentMock->getAttribute(Argument::type('string'))->will(function($args) use($paymentMock) {
            if ($args[0] == 'caseable_type') {
                return HospitalAccident::class;
            }
            if ($args[0] == 'caseable_id') {
                return 5;
            }
            if ($args[0] == 'paymentFromAssistant') {
                return $paymentMock->reveal();
            }
            return null;
        });
        /** @var Accident $accident */
        $accident = $accidentMock->reveal();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 3,
            'financeConditionServiceMock->findConditions' => 1,
            'formulaServiceMock->createFormulaFromConditions' => 1,
        ])->getIncomeFormula($accident));
    }
}
