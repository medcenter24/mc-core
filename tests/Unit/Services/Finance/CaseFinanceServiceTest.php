<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\Tests\Unit\Services\Finance;


use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\Contract\Formula\FormulaBuilder;
use medcenter24\mcCore\App\DoctorAccident;
use medcenter24\mcCore\App\HospitalAccident;
use medcenter24\mcCore\App\Invoice;
use medcenter24\mcCore\App\Payment;
use medcenter24\mcCore\App\Services\AccidentService;
use medcenter24\mcCore\App\Services\CaseServices\Finance\CaseFinanceService;
use medcenter24\mcCore\App\Services\CurrencyService;
use medcenter24\mcCore\App\Services\FinanceConditionService;
use medcenter24\mcCore\App\Services\Formula\FormulaService;
use Illuminate\Support\Collection;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use medcenter24\mcCore\Tests\TestCase;

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
        $formulaServiceMock->createFormulaFromConditions(Argument::type(Collection::class))
            ->shouldBeCalledTimes($this->getExpectation('formulaServiceMock->createFormulaFromConditions', $expects))
            ->willReturn($formulaBuilder);
        /** @var FormulaService $formulaService */
        $formulaService = $formulaServiceMock->reveal();

        $accidentServiceMock = $this->prophesize(AccidentService::class);

        $accidentServiceMock->getAccidentServices(Argument::type(Accident::class))->willReturn(collect());

        /** @var AccidentService $accidentService */
        $accidentService = $accidentServiceMock->reveal();

        /** @var FinanceConditionService|ObjectProphecy $financeConditionServiceMock */
        $financeConditionServiceMock = $this->prophesize(FinanceConditionService::class);
        $financeConditionServiceMock->findConditions(Argument::type('string'), Argument::type('array'))
            ->shouldBeCalledTimes($this->getExpectation('financeConditionServiceMock->findConditions', $expects))
            ->will(function ($args) {
            if (array_key_exists(DoctorAccident::class, $args[1])) {
                if (!$args[1][DoctorAccident::class]) {
                    return collect([]);
                }
                return collect([1]);
            }

            if (array_key_exists(HospitalAccident::class, $args[1])) {
                if (!$args[1][HospitalAccident::class]) {
                    return collect([]);
                }
                return collect([1]);
            }

            return collect([]);
        });

        /** @var FinanceConditionService $financeConditionService */
        $financeConditionService = $financeConditionServiceMock->reveal();

        $currencyServiceMock = $this->prophesize(CurrencyService::class);
        /** @var CurrencyService $currencyService */
        $currencyService = $currencyServiceMock->reveal();

        return new CaseFinanceService($formulaService, $accidentService, $financeConditionService, $currencyService);
    }

    private function getAccidentMock(): ObjectProphecy
    {
        return $this->prophesize(Accident::class);
    }

    /**
     * @return ObjectProphecy
     */
    private function getDoctorAccidentMock(): ObjectProphecy
    {
        $accidentMock = $this->getAccidentMock();
        $accidentMock->isDoctorCaseable()->willReturn(true);
        $accidentMock->isHospitalCaseable()->willReturn(false);
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
        $hospitalCase = $this->prophesize(HospitalAccident::class);

        $accidentMock = $this->getAccidentMock();
        $accidentMock->isHospitalCaseable()->willReturn(true);
        $accidentMock->isDoctorCaseable()->willReturn(false);
        $accidentMock->getAttribute(Argument::type('string'))->will(function ($args) use ($hospitalCase) {
            $value = null;
            switch ($args['0']) {
                case 'caseable_type':
                    $value = HospitalAccident::class;
                    break;
                case 'caseable':
                    $value = $hospitalCase->reveal();
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
        ])->getToDoctorFormula($accident), 'Doctors payment is correct');
        // how much has assistant paid us?
        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'formulaServiceMock->createFormulaFromConditions' => 0,
        ])->getFromAssistantFormula($accident), 'Assistants payment is correct');
    }

    /**
     * Case without anything should return 0 for all valuable variables
     * @throws \medcenter24\mcCore\App\Exceptions\InconsistentDataException
     * @throws \medcenter24\mcCore\App\Models\Formula\Exception\FormulaException
     */
    public function testEmptyHospitalCase(): void
    {
        /** @var Accident $accident */
        $accident = $this->getHospitalAccidentMock()->reveal();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
        ])->getToHospitalFormula($accident), 'Payment is correct');
        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'formulaServiceMock->createFormulaFromConditions' => 0,
        ])->getFromAssistantFormula($accident), 'Assistants payment is correct');
    }

    /**
     * @expectedException \medcenter24\mcCore\App\Exceptions\InconsistentDataException
     * @expectedExceptionMessage Hospital Case only
     * @throws \medcenter24\mcCore\App\Exceptions\InconsistentDataException
     * @throws \medcenter24\mcCore\App\Models\Formula\Exception\FormulaException
     */
    public function testWrongHospitalCaseException(): void
    {
        /** @var Accident $accident */
        $accident = $this->getDoctorAccidentMock()->reveal();
        $this->financeService()->getToHospitalFormula($accident);
    }

    /**
     * @expectedException \medcenter24\mcCore\App\Exceptions\InconsistentDataException
     * @expectedExceptionMessage DoctorAccident only
     * @throws \medcenter24\mcCore\App\Exceptions\InconsistentDataException
     * @throws \medcenter24\mcCore\App\Models\Formula\Exception\FormulaException
     */
    public function testWrongDoctorCaseException(): void
    {
        /** @var Accident $accident */
        $accident = $this->getHospitalAccidentMock()->reveal();
        $this->financeService()->getToDoctorFormula($accident);
    }

    /**
     * When the payment already stored (when we had static digit in the DB) - Doesn't need to calculate again
     * @throws \medcenter24\mcCore\App\Models\Formula\Exception\FormulaException
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
        ])->getFromAssistantFormula($accident));
    }

    /**
     * This is payment that needs to be calculated according to the conditions
     * @throws \medcenter24\mcCore\App\Models\Formula\Exception\FormulaException
     */
    public function testCalculateFromAssistantEmptyPayment(): void
    {
        $accidentMock = $this->getAccidentMock();
        /** @var Accident $accident */
        $accident = $accidentMock->reveal();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
        ])->getFromAssistantFormula($accident));
    }

    /**
     * This is payment that needs to be calculated according to the conditions
     * @throws \medcenter24\mcCore\App\Models\Formula\Exception\FormulaException
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
        ])->getFromAssistantFormula($accident));
    }

    /**
     * @throws \medcenter24\mcCore\App\Exceptions\InconsistentDataException
     * @throws \medcenter24\mcCore\App\Models\Formula\Exception\FormulaException
     */
    public function testCalculateToDoctorPayment(): void
    {
        $accidentMock = $this->getAccidentMock();
        $accidentMock->isDoctorCaseable()->willReturn(true);
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
        ])->getToDoctorFormula($accident));
    }

    /**
     * @throws \medcenter24\mcCore\App\Exceptions\InconsistentDataException
     * @throws \medcenter24\mcCore\App\Models\Formula\Exception\FormulaException
     */
    public function testCalculateToDoctorPaymentWithCondition(): void
    {
        $accidentMock = $this->getAccidentMock();
        $accidentMock->isDoctorCaseable()->willReturn(true);
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
        ])->getToDoctorFormula($accident));
    }

    /**
     * @throws \medcenter24\mcCore\App\Exceptions\InconsistentDataException
     * @throws \medcenter24\mcCore\App\Models\Formula\Exception\FormulaException
     */
    public function testStoredToDoctorPayment(): void
    {
        $paymentMock = $this->prophesize(Payment::class);
        $paymentMock->getAttribute(Argument::type('string'))->willReturn(10);

        $accidentMock = $this->getAccidentMock();
        $accidentMock->isDoctorCaseable()->willReturn(true);
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
        ])->getToDoctorFormula($accident));
    }

    /**
     * @throws \medcenter24\mcCore\App\Exceptions\InconsistentDataException
     * @throws \medcenter24\mcCore\App\Models\Formula\Exception\FormulaException
     */
    public function testCalculateToHospitalPayment(): void
    {
        $accidentMock = $this->getAccidentMock();
        $accidentMock->isDoctorCaseable()->willReturn(false);
        $accidentMock->isHospitalCaseable()->willReturn(true);
        $accidentMock->getAttribute(Argument::type('string'))->will(function($args) {
            $res = '';
            switch ($args[0]) {
                case 'caseable_type':
                    $res = HospitalAccident::class;
                    break;
                case 'caseable_id':
                    $res = 0;
                    break;
                case 'caseable':
                    $res = false;
                    break;
            }

            return $res;
        });
        /** @var Accident $accident */
        $accident = $accidentMock->reveal();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
        ])->getToHospitalFormula($accident));
    }

    /**
     * @throws \medcenter24\mcCore\App\Exceptions\InconsistentDataException
     * @throws \medcenter24\mcCore\App\Models\Formula\Exception\FormulaException
     */
    public function testCalculateToHospitalPaymentWithCondition(): void
    {
        $accidentMock = $this->getAccidentMock();
        $accidentMock->isHospitalCaseable()->willReturn(true);
        $accidentMock->getAttribute(Argument::type('string'))->will(function($args) {
            $res = null;
            switch ($args[0]) {
                case 'caseable_type':
                    $res = HospitalAccident::class;
                    break;
                case 'caseable_id':
                    $res = 5;
                    break;
                case 'caseable':
                    $res = false;
                    break;
            }

            return $res;
        });
        /** @var Accident $accident */
        $accident = $accidentMock->reveal();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
            'formulaServiceMock->createFormulaFromConditions' => 1,
        ])->getToHospitalFormula($accident));
    }

    /**
     * @throws \medcenter24\mcCore\App\Exceptions\InconsistentDataException
     * @throws \medcenter24\mcCore\App\Models\Formula\Exception\FormulaException
     */
    public function testStoredToHospitalPayment(): void
    {
        $paymentMock = $this->prophesize(Payment::class);
        $paymentMock->getAttribute(Argument::type('string'))->willReturn(10);

        $accidentMock = $this->getAccidentMock();
        $accidentMock->isHospitalCaseable()->willReturn(true);
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
        ])->getToHospitalFormula($accident));
    }
}
