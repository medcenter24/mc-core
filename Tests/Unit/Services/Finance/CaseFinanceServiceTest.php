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

declare(strict_types = 1);

namespace medcenter24\mcCore\Tests\Unit\Services\Finance;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Contract\Formula\FormulaBuilder;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Entity\Invoice;
use medcenter24\mcCore\App\Models\Formula\Exception\FormulaException;
use medcenter24\mcCore\App\Entity\Payment;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\CaseServices\Finance\CaseFinanceService;
use medcenter24\mcCore\App\Services\Entity\CurrencyService;
use medcenter24\mcCore\App\Services\Entity\FinanceConditionService;
use medcenter24\mcCore\App\Services\Formula\FormulaService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use medcenter24\mcCore\Tests\TestCase;

class CaseFinanceServiceTest extends TestCase
{
    private function getExpectation($key, $list): int
    {
        return array_key_exists($key, $list) ? (int)$list[$key] : 0;
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

        /** @var MockObject|FormulaService $formulaServiceMock */
        $formulaServiceMock = $this->createMock(FormulaService::class);
        $formulaServiceMock
            ->expects(self::exactly($this->getExpectation('formulaServiceMock->createFormula', $expects)))
            ->method('createFormula')
            ->willReturn($formulaBuilder);
        $formulaServiceMock
            ->expects(self::exactly($this->getExpectation('formulaServiceMock->createFormulaFromConditions', $expects)))
            ->method('createFormulaFromConditions')
            ->with($this->isInstanceOf(Collection::class))
            ->willReturn($formulaBuilder);

        /** @var MockObject|AccidentService $accidentServiceMock */
        $accidentServiceMock = $this->createMock(AccidentService::class);
        $accidentServiceMock
            ->method('getAccidentServices')
            ->willReturn(collect());

        /** @var FinanceConditionService|MockObject $financeConditionServiceMock */
        $financeConditionServiceMock = $this->createMock(FinanceConditionService::class);
        $financeConditionServiceMock
            ->expects(self::exactly($this->getExpectation('financeConditionServiceMock->findConditions', $expects)))
            ->method('findConditions')
            ->willReturnCallback(function ($className, $collection) {
                if (array_key_exists(DoctorAccident::class, $collection)) {
                    if (!$collection[DoctorAccident::class]) {
                        return collect([]);
                    }
                    return collect([1]);
                }

                if (array_key_exists(HospitalAccident::class, $collection)) {
                    if (!$collection[HospitalAccident::class]) {
                        return collect([]);
                    }
                    return collect([1]);
                }

                return collect([]);
            });

        /** @var MockObject|CurrencyService $currencyServiceMock */
        $currencyServiceMock = $this->createMock(CurrencyService::class);

        $mockedServiceLocator = $this->mockServiceLocator([
            FormulaService::class => $formulaServiceMock,
            AccidentService::class => $accidentServiceMock,
            FinanceConditionService::class => $financeConditionServiceMock,
            CurrencyService::class => $currencyServiceMock,
        ]);

        $caseFinanceService =  new CaseFinanceService();
        $caseFinanceService->setServiceLocator($mockedServiceLocator);
        return $caseFinanceService;
    }

    private function getAccidentMock(): MockObject
    {
        return $this->createMock(Accident::class);
    }

    private function getDoctorAccidentMock(): MockObject
    {
        /** @var MockObject|Accident $accidentMock */
        $accidentMock = $this->getAccidentMock();
        $accidentMock
            ->method('isDoctorCaseable')
            ->willReturn(true);
        $accidentMock
            ->method('isHospitalCaseable')
            ->willReturn(false);
        $doctorAccidentMock = $this->createMock(DoctorAccident::class);
        $accidentMock->method('getAttribute')
            ->willReturnCallback(function ($name) use ($doctorAccidentMock) {
                return match ($name) {
                    'caseable_type' => DoctorAccident::class,
                    'caseable' => $doctorAccidentMock->method('getAttribute')->willReturn(collect()),
                    default => null,
                };
            });
        return $accidentMock;
    }

    private function getHospitalAccidentMock(): MockObject
    {
        $hospitalCase = $this->createMock(HospitalAccident::class);

        $accidentMock = $this->getAccidentMock();
        $accidentMock
            ->method('isHospitalCaseable')
            ->willReturn(true);
        $accidentMock
            ->method('isDoctorCaseable')
            ->willReturn(false);
        $accidentMock
            ->method('getAttribute')
            ->willReturnCallback(
                function ($name) use ($hospitalCase) {
                    return match ($name) {
                        AccidentService::FIELD_CASEABLE_TYPE => HospitalAccident::class,
                        'caseable' => $hospitalCase,
                        default => null,
                    };
                });

        return $accidentMock;
    }

    /**
     * Case without anything should return 0 for all valuable variables
     * @throws InconsistentDataException
     * @throws FormulaException
     */
    public function testEmptyDoctorCase(): void
    {
        /** @var Accident $accident */
        $accident = $this->getDoctorAccidentMock();

        // how much do we need to pay to the doctor?
        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getToDoctorFormula($accident), 'Doctors payment is correct');

        // how much has assistant paid us?
        $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'formulaServiceMock->createFormulaFromConditions' => 0,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getFromAssistantFormula($accident);
    }

    /**
     * Case without anything should return 0 for all valuable variables
     * @throws InconsistentDataException
     * @throws FormulaException
     */
    public function testEmptyHospitalCase(): void
    {
        /** @var Accident $accident */
        $accident = $this->getHospitalAccidentMock();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getToHospitalFormula($accident), 'Payment is correct');
        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'formulaServiceMock->createFormulaFromConditions' => 0,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getFromAssistantFormula($accident), 'Assistants payment is correct');
    }

    /**
     * @throws InconsistentDataException
     * @throws FormulaException
     */
    public function testWrongHospitalCaseException(): void
    {
        $this->expectException(InconsistentDataException::class);
        $this->expectExceptionMessage('Hospital Case only');

        /** @var Accident $accident */
        $accident = $this->getDoctorAccidentMock();
        $this->financeService()->getToHospitalFormula($accident);
    }

    /**
     * @throws InconsistentDataException
     * @throws FormulaException
     */
    public function testWrongDoctorCaseException(): void
    {
        $this->expectException(InconsistentDataException::class);
        $this->expectExceptionMessage('DoctorAccident only');

        /** @var Accident $accident */
        $accident = $this->getHospitalAccidentMock();
        $this->financeService()->getToDoctorFormula($accident);
    }

    /**
     * When the payment already stored (when we had static digit in the DB) - Doesn't need to calculate again
     * @throws FormulaException
     */
    public function testStoredFromAssistantPayment(): void
    {
        $paymentMock = $this->createMock(Payment::class);
        $paymentMock
            ->method('getAttribute')
            ->willReturn(10);

        /** @var Accident|MockObject $accidentMock */
        $accidentMock = $this->getAccidentMock();
        $accidentMock
            ->method('getAttribute')
            ->willReturnCallback(function($name) use($paymentMock) {
                return $name === 'paymentFromAssistant' ? $paymentMock : null;
            });

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getFromAssistantFormula($accidentMock));
    }

    /**
     * This is payment that needs to be calculated according to the conditions
     * @throws FormulaException
     */
    public function testCalculateFromAssistantEmptyPayment(): void
    {
        /** @var Accident $accidentMock */
        $accidentMock = $this->getAccidentMock();

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getFromAssistantFormula($accidentMock));
    }

    /**
     * This is payment that needs to be calculated according to the conditions
     * @throws FormulaException
     */
    public function testCalculateFromAssistantPaymentWithCondition(): void
    {
        $paymentMock = $this->createMock(Payment::class);
        $paymentMock
            ->method('getAttribute')
            ->willReturn(987);

        $assistantGuaranteeMock = $this->createMock(Invoice::class);
        $assistantGuaranteeMock
            ->method('getAttribute')
            ->willReturnCallback(function($name) use ($paymentMock){
                if ($name == 'payment') {
                    return $paymentMock;
                }
                return null;
            });

        /** @var Accident|MockObject $accidentMock */
        $accidentMock = $this->getAccidentMock();
        $accidentMock
            ->method('getAttribute')
            ->willReturnCallback(function($name) use ($assistantGuaranteeMock) {
                return match ($name) {
                    'assistantGuarantee' => $assistantGuaranteeMock,
                    'caseable_id' => 5,
                    default => null,
                };
            });

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getFromAssistantFormula($accidentMock));
    }

    /**
     * @throws InconsistentDataException
     * @throws FormulaException
     */
    public function testCalculateToDoctorPayment(): void
    {
        /** @var Accident|MockObject $accidentMock */
        $accidentMock = $this->getAccidentMock();
        $accidentMock
            ->method('isDoctorCaseable')
            ->willReturn(true);
        $accidentMock
            ->method('getAttribute')
            ->willReturnCallback(function($name) {
                return match ($name) {
                    'caseable_type' => DoctorAccident::class,
                    'caseable_id' => 0,
                    default => null,
                };
            });

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getToDoctorFormula($accidentMock));
    }

    /**
     * @throws InconsistentDataException
     * @throws FormulaException
     */
    public function testCalculateToDoctorPaymentWithCondition(): void
    {
        /** @var Accident|MockObject $accidentMock */
        $accidentMock = $this->getAccidentMock();
        $accidentMock
            ->method('isDoctorCaseable')
            ->willReturn(true);

        $accidentMock
            ->method('getAttribute')
            ->willReturnCallback(function($name) {
                return match ($name) {
                    'caseable_type' => DoctorAccident::class,
                    'caseable_id' => 5,
                    default => null,
                };
            });

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getToDoctorFormula($accidentMock));
    }

    /**
     * @throws InconsistentDataException
     * @throws FormulaException
     */
    public function testStoredToDoctorPayment(): void
    {
        $paymentMock = $this->createMock(Payment::class);
        $paymentMock
            ->method('getAttribute')
            ->willReturn(10);

        /** @var Accident|MockObject $accidentMock */
        $accidentMock = $this->getAccidentMock();
        $accidentMock
            ->method('isDoctorCaseable')
            ->willReturn(true);
        $accidentMock
            ->method('getAttribute')
            ->willReturnCallback(function($name) use($paymentMock) {
                return match ($name) {
                    'caseable_type' => DoctorAccident::class,
                    'paymentToCaseable' => $paymentMock,
                    default => null,
                };
            });

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getToDoctorFormula($accidentMock));
    }

    /**
     * @throws InconsistentDataException
     * @throws FormulaException
     */
    public function testCalculateToHospitalPayment(): void
    {
        /** @var Accident|MockObject $accidentMock */
        $accidentMock = $this->getAccidentMock();
        $accidentMock
            ->method('isDoctorCaseable')
            ->willReturn(false);
        $accidentMock
            ->method('isHospitalCaseable')
            ->willReturn(true);
        $accidentMock
            ->method('getAttribute')
            ->willReturnCallback(function($name) {
                return match ($name) {
                    'caseable_type' => HospitalAccident::class,
                    'caseable_id' => 0,
                    'caseable' => false,
                    default => '',
                };
            });

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getToHospitalFormula($accidentMock));
    }

    /**
     * @throws InconsistentDataException
     * @throws FormulaException
     */
    public function testCalculateToHospitalPaymentWithCondition(): void
    {
        /** @var MockObject|Accident $accidentMock */
        $accidentMock = $this->getAccidentMock();
        $accidentMock
            ->method('isHospitalCaseable')
            ->willReturn(true);
        $accidentMock
            ->method('getAttribute')
            ->willReturnCallback(function($name) {
                return match ($name) {
                    'caseable_type' => HospitalAccident::class,
                    'caseable_id' => 5,
                    'caseable' => false,
                    default => null,
                };
            });

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getToHospitalFormula($accidentMock));
    }

    /**
     * @throws InconsistentDataException
     * @throws FormulaException
     */
    public function testStoredToHospitalPayment(): void
    {
        $paymentMock = $this->createMock(Payment::class);
        $paymentMock
            ->method('getAttribute')
            ->willReturn(10);

        $accidentMock = $this->getAccidentMock();
        $accidentMock
            ->method('isHospitalCaseable')
            ->willReturn(true);
        $accidentMock
            ->method('getAttribute')
            ->willReturnCallback(function($name) use($paymentMock) {
                return match ($name) {
                    'caseable_type' => HospitalAccident::class,
                    'paymentToCaseable' => $paymentMock,
                    default => null,
                };
            });

        self::assertInstanceOf(FormulaBuilder::class, $this->financeService([
            'formulaServiceMock->createFormula' => 1,
            'financeConditionServiceMock->findConditions' => 1,
        ])->getToHospitalFormula($accidentMock));
    }

}
