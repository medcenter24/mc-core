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

namespace medcenter24\mcCore\Tests\Feature\Api\Director\Cases\CaseController;

use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\City;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\FinanceCondition;
use medcenter24\mcCore\App\Entity\FinanceCurrency;
use medcenter24\mcCore\App\Entity\FinanceStorage;
use medcenter24\mcCore\App\Entity\Hospital;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Entity\Invoice;
use medcenter24\mcCore\App\Entity\Payment;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class CaseControllerFinanceActionTest extends TestCase
{
    use DirectorTestTraitApi;

    /**
     * @var AccidentService
     */
    private $accidentService;
    /**
     * @var AccidentStatusService
     */
    private $accidentStatusService;

    public function setUp(): void
    {
        parent::setUp();

        $this->accidentService = new AccidentService();
        $this->accidentStatusService = new AccidentStatusService();
    }

    /**
     * @param array $data
     * @return Accident
     */
    private function createNewAccident(array $data = []): Model
    {
        return $this->accidentService->create(array_merge($data, [
            'accident_status_id' => $this->accidentStatusService->getNewStatus()->getAttribute('id'),
        ]));
    }

    /**
     * @return Accident
     */
    private function createNewDoctorCase(): Model
    {
        $caseable = factory(DoctorAccident::class)->create();
        $accident = $this->createNewAccident([
            'caseable_type' => DoctorAccident::class,
            'caseable_id' => $caseable->getAttribute('id'),
        ]);
        return $accident;
    }

    public function test404(): void {
        $response = $this->sendGet('/api/director/cases/1/finance');
        $response->assertStatus(404);
        $response->assertJson([]);
    }

    public function testWithoutCondition(): void {
        $accident = $this->createNewAccident();
        factory(FinanceCurrency::class)->create();
        $response = $this->sendGet('/api/director/cases/'.$accident->id.'/finance');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'currency' => [],
                    'formula' => '0.00 - 0.00',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'currency' => [],
                    'formula' => '0.00',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'currency' => [],
                    'formula' => '0.00',
                ],
            ],
        ]);
    }

    /**
     * Global condition for each accident price
     * Assistant should pay, because someone have to
     */
    public function testAssistantCondition(): void
    {
        $accident = $this->createNewAccident();
        factory(FinanceCurrency::class)->create();

        // condition
        // each accident has price 10
        factory(FinanceCondition::class)->create([
            'type' => 'add',
            'value' => '10',
            'currency_id' => 1,
            'currency_mode' => 'currency',
            'model' => Assistant::class,
        ]);

        $response = $this->sendGet('/api/director/cases/'.$accident->id.'/finance');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'calculatedValue' => 10,
                    'currency' => [],
                    'formula' => '10.00 - 0.00',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'calculatedValue' => 10,
                    'currency' => [],
                    'formula' => '10.00',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'calculatedValue' => 0,
                    'currency' => [],
                    'formula' => '0.00',
                ],
            ],
        ]);
    }

    /**
     * Global condition for each accident price
     * Assistant should pay, because someone have to
     */
    public function testStoredAssistantCondition(): void
    {
        $city = factory(City::class)->create();
        $city2 = factory(City::class)->create();
        $accident = $this->createNewAccident(['city_id' => $city->id]);
        $currency = factory(FinanceCurrency::class)->create();

        // condition
        // each accident has price 10
        FinanceCondition::create([
            'title' => 'test',
            'type' => 'add',
            'value' => '10',
            'currency_id' => $currency->id,
            'currency_mode' => 'currency',
            'model' => Assistant::class,
        ]);

        // condition for the city
        FinanceStorage::create([
            'finance_condition_id' => FinanceCondition::create([
                'title' => 'test',
                'type' => 'sub',
                'value' => '1',
                'currency_id' => $currency->id,
                'currency_mode' => 'percent',
                'model' => Assistant::class,
            ])->id,
            'model' => City::class,
            'model_id' => $city->id,
        ]);

        // condition for the city2
        FinanceStorage::create([
            'finance_condition_id' => FinanceCondition::create([
                'title' => 'test',
                'type' => 'add',
                'value' => '500',
                'currency_id' => $currency->id,
                'currency_mode' => 'currency',
                'model' => Assistant::class,
            ])->id,
            'model' => City::class,
            'model_id' => $city2->id,
        ]);

        // second condition for the city
        FinanceStorage::create([
            'finance_condition_id' => FinanceCondition::create([
                'title' => 'test',
                'type' => 'add',
                'value' => '7',
                'currency_id' => $currency->id,
                'currency_mode' => 'currency',
                'model' => Assistant::class,
            ]),
            'model' => City::class,
            'model_id' => $city->id,
        ]);

        $response = $this->sendGet('/api/director/cases/'.$accident->id.'/finance');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'calculatedValue' => 16.83,
                    'currency' => [],
                    'formula' => '16.83 - 0.00',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'calculatedValue' => 16.83,
                    'currency' => [],
                    'formula' => '( 10.00 + 7.00 ) * 99%',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'calculatedValue' => 0,
                    'currency' => [],
                    'formula' => '0.00',
                ],
            ],
        ]);
    }

    public function testDoctorCondition(): void
    {
        $accident = $this->createNewDoctorCase();
        $currency = factory(FinanceCurrency::class)->create();

        // condition
        // each doctors accident has price 10
        factory(FinanceCondition::class)->create([
            'type' => 'add',
            'value' => '10',
            'currency_id' => $currency->getAttribute('id'),
            'currency_mode' => 'currency',
            'model' => Doctor::class,
        ]);

        $response = $this->sendGet('/api/director/cases/'.$accident->id.'/finance');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'calculatedValue' => -10,
                    'currency' => [],
                    'formula' => '0.00 - 10.00',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'calculatedValue' => 0,
                    'currency' => [],
                    'formula' => '0.00',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'calculatedValue' => 10,
                    'currency' => [],
                    'formula' => '10.00',
                ],
            ],
        ]);
    }

    /**
     * Both doctors and assistant conditions
     */
    public function testIncomeAssistantDoctorConditions(): void
    {
        $accident = $this->createNewDoctorCase();
        $currency = factory(FinanceCurrency::class)->create();

        // condition
        // each accident has price 10
        FinanceCondition::create([
            'title' => 'test',
            'type' => 'add',
            'value' => '10',
            'currency_id' => $currency->id,
            'currency_mode' => 'currency',
            'model' => Assistant::class,
        ]);

        // each accident has price +3450
        FinanceCondition::create([
            'title' => 'test',
            'type' => 'add',
            'value' => '3450',
            'currency_id' => $currency->id,
            'currency_mode' => 'currency',
            'model' => Assistant::class,
        ]);

        // each doctor's case have to be smaller to 5%
        FinanceCondition::create([
            'title' => 'test',
            'type' => 'add',
            'value' => '5',
            'currency_id' => $currency->id,
            'currency_mode' => 'percent',
            'model' => Assistant::class,
        ]);

        // each doctor's case have to be smaller to +5%
        FinanceCondition::create([
            'title' => 'test',
            'type' => 'add',
            'value' => '5',
            'currency_id' => $currency->id,
            'currency_mode' => 'percent',
            'model' => Assistant::class,
        ]);

        // each doctor's case have to be paid by 4.99
        FinanceCondition::create([
            'title' => 'test',
            'type' => 'add',
            'value' => '4.991',
            'currency_id' => $currency->id,
            'currency_mode' => 'currency',
            'model' => Doctor::class,
        ]);

        // each doctor's case have to be also paid by 2039
        FinanceCondition::create([
            'title' => 'test',
            'type' => 'add',
            'value' => '2039',
            'currency_id' => $currency->id,
            'currency_mode' => 'currency',
            'model' => Doctor::class,
        ]);

        // each doctor's case have to be smaller to 5%
        FinanceCondition::create([
            'title' => 'test',
            'type' => 'add',
            'value' => '5',
            'currency_id' => $currency->id,
            'currency_mode' => 'percent',
            'model' => Doctor::class,
        ]);

        $response = $this->sendGet('/api/director/cases/'.$accident->id.'/finance');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'calculatedValue' => 1659.81,
                    'currency' => [],
                    'formula' => '3806.00 - 2146.19',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'calculatedValue' => 3806.0000000000005,
                    'currency' => [],
                    'formula' => '( 10.00 + 3450.00 ) * 110%',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'calculatedValue' => 2146.1895,
                    'currency' => [],
                    'formula' => '( 4.99 + 2039.00 ) * 105%',
                ],
            ],
        ]);
    }

    public function testAssistantDoctorComplexCondition(): void
    {
        $accident = $this->createNewDoctorCase();
        factory(FinanceCurrency::class)->create();

        // condition
        // each accident has price 10
        factory(FinanceCondition::class)->create([
            'type' => 'add',
            'value' => '10',
            'currency_id' => 1,
            'currency_mode' => 'currency',
            'model' => Assistant::class,
        ]);
        // each doctor will be paid for 4.99
        factory(FinanceCondition::class)->create([
            'type' => 'add',
            'value' => '4.991',
            'currency_id' => 1,
            'currency_mode' => 'currency',
            'model' => Doctor::class,
        ]);

        $response = $this->sendGet('/api/director/cases/'.$accident->id.'/finance');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'calculatedValue' => 5.01,
                    'currency' => [],
                    'formula' => '10.00 - 4.99',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'calculatedValue' => 10,
                    'currency' => [],
                    'formula' => '10.00',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'calculatedValue' => 4.99,
                    'currency' => [],
                    'formula' => '4.99',
                ],
            ],
        ]);
    }

    /**
     * Checks that Hospital prices are taken from the invoices from the hospital or stored as a fixed payment
     */
    public function testHospitalCondition(): void
    {
        $caseable = factory(HospitalAccident::class)->create();
        $accident = $this->createNewAccident([
            'caseable_type' => HospitalAccident::class,
            'caseable_id' => $caseable->id,
        ]);
        $currency = factory(FinanceCurrency::class)->create();

        // condition
        // each accident has price 10
        factory(FinanceCondition::class)->create([
            'type' => 'add',
            'value' => '10',
            'currency_id' => $currency->id,
            'currency_mode' => 'currency',
            'model' => Hospital::class,
        ]);

        $response = $this->sendGet('/api/director/cases/'.$accident->id.'/finance');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'calculatedValue' => -10,
                    'currency' => [],
                    'formula' => '0.00 - 10.00',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'calculatedValue' => 0,
                    'currency' => [],
                    'formula' => '0.00',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'calculatedValue' => 10,
                    'currency' => [],
                    'formula' => '10.00',
                ],
            ],
        ]);
    }

    /**
     * Checks that Hospital prices are taken from the invoice only
     * If invoice provided it needs to be paid, nothing more can be added there
     */
    public function testHospitalInvoiceCondition(): void
    {
        $caseable = factory(HospitalAccident::class)->create();
        $currency = factory(FinanceCurrency::class)->create();
        $payment = factory(Payment::class)->create([
            'currency_id' => $currency->id,
            'fixed' => 0,
            'value' => 5
        ]);
        $invoice = factory(Invoice::class)->create([
            'payment_id' => $payment->id,
        ]);
        $accident = $accident = $this->createNewAccident([
            'caseable_type' => HospitalAccident::class,
            'caseable_id' => $caseable->id,
            'assistant_invoice_id' => $invoice->id,
        ]);

        // condition
        // each accident costs for the hospital 10
        factory(FinanceCondition::class)->create([
            'type' => 'add',
            'value' => '10',
            'currency_id' => $currency->id,
            'currency_mode' => 'currency',
            'model' => Hospital::class,
        ]);

        $response = $this->sendGet('/api/director/cases/'.$accident->id.'/finance');
        $response->assertStatus(200);


        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'calculatedValue' => -5,
                    'currency' => [],
                    'formula' => '5.00 - 10.00',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'calculatedValue' => 0,
                    'currency' => [],
                    'payment' => [
                        'value' => '5',
                    ],
                    'formula' => 'invoice',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'calculatedValue' => 10,
                    'currency' => [],
                    'formula' => '10.00',
                ],
            ],
        ]);
    }

    /**
     * Checks that Hospital prices are taken from the invoices from the hospital or stored as a fixed payment
     */
    public function testHospitalFixedPayment(): void
    {
        $caseable = factory(HospitalAccident::class)->create(['hospital_invoice_id' => 0]);
        $currency = factory(FinanceCurrency::class)->create();
        $payment = factory(Payment::class)->create([
            'currency_id' => $currency->id,
            'fixed' => 0,
            'value' => 5
        ]);
        $caseablePayment = factory(Payment::class)->create([
            'currency_id' => $currency->id,
            'fixed' => 1,
            'value' => -2
        ]);
        $invoice = factory(Invoice::class)->create([
            'payment_id' => $payment->id,
        ]);
        $accident = $accident = $this->createNewAccident([
            'caseable_type' => HospitalAccident::class,
            'caseable_id' => $caseable->id,
            'assistant_invoice_id' => $invoice->id,
            'caseable_payment_id' => $caseablePayment->id,
        ]);

        // condition
        // each accident has price 10
        factory(FinanceCondition::class)->create([
            'type' => 'add',
            'value' => '10',
            'currency_id' => $currency->id,
            'currency_mode' => 'currency',
            'model' => Hospital::class,
        ]);

        $response = $this->sendGet('/api/director/cases/'.$accident->id.'/finance');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'calculatedValue' => 7,
                    'currency' => [],
                    'formula' => '5.00 - -2.00',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'calculatedValue' => 0,
                    'currency' => [],
                    'payment' => [
                        'value' => $payment->value,
                    ],
                    'formula' => 'invoice',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'currency' => [],
                    'payment' =>
                        array (
                            'id' => 2,
                            'createdBy' => 0,
                            'value' => -2,
                            'currencyId' => $currency->id,
                            'fixed' => true,
                            'description' => 'Faker factory',
                        ),
                    'formula' => 'fixed',
                    'calculatedValue' => 0,
                ],
            ],
        ]);
    }

    public function testFixedIncome(): void
    {
        $caseable = factory(HospitalAccident::class)->create();
        $currency = factory(FinanceCurrency::class)->create();
        $payment = factory(Payment::class)->create([
            'currency_id' => $currency->id,
            'fixed' => 0,
            'value' => 5
        ]);
        $caseablePayment = factory(Payment::class)->create([
            'currency_id' => $currency->id,
            'fixed' => 1,
            'value' => -2
        ]);
        $invoice = factory(Invoice::class)->create([
            'payment_id' => $payment->id,
        ]);
        $incomePayment = factory(Payment::class)->create([
            'currency_id' => $currency->id,
            'fixed' => 1,
            'value' => 700,
        ]);
        $accident = $accident = $this->createNewAccident([
            'caseable_type' => HospitalAccident::class,
            'caseable_id' => $caseable->id,
            'assistant_invoice_id' => $invoice->id,
            'caseable_payment_id' => $caseablePayment->id,
            'income_payment_id' => $incomePayment->id,
        ]);

        // condition
        // each accident has price 10
        factory(FinanceCondition::class)->create([
            'type' => 'add',
            'value' => '10',
            'currency_id' => $currency->id,
            'currency_mode' => 'currency',
            'model' => Hospital::class,
        ]);

        $response = $this->sendGet('/api/director/cases/'.$accident->id.'/finance');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'calculatedValue' => 0,
                    'currency' => [],
                    'formula' => 'fixed',
                    'payment' => [
                        'id' => $incomePayment->id,
                    ]
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'calculatedValue' => 0,
                    'currency' => [],
                    'formula' => 'invoice',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'payment' => [
                            'id' => $caseablePayment->id,
                        ],
                    'formula' => 'fixed',
                    'calculatedValue' => 0,
                ],
            ],
        ]);
    }

    /**
     * FixedFromAssistant - FixedToTheCaseable
     */
    public function testIncomeFromFixed(): void
    {
        $caseable = factory(HospitalAccident::class)->create();
        $currency = factory(FinanceCurrency::class)->create();
        $payment = factory(Payment::class)->create([
            'currency_id' => $currency->id,
            'fixed' => 0,
            'value' => 5
        ]);
        $caseablePayment = factory(Payment::class)->create([
            'currency_id' => $currency->id,
            'fixed' => 1,
            'value' => 5
        ]);
        $invoice = factory(Invoice::class)->create([
            'payment_id' => $payment->id,
        ]);
        $incomePayment = factory(Payment::class)->create([
            'currency_id' => $currency->id,
            'fixed' => 0,
            'value' => 700,
        ]);
        $assistantPayment = factory(Payment::class)->create([
            'currency_id' => $currency->id,
            'fixed' => 1,
            'value' => 20,
        ]);
        $accident = $accident = $this->createNewAccident([
            'caseable_type' => HospitalAccident::class,
            'caseable_id' => $caseable->id,
            'assistant_invoice_id' => $invoice->id,
            'caseable_payment_id' => $caseablePayment->id,
            'income_payment_id' => $incomePayment->id,
            'assistant_payment_id' => $assistantPayment->id,
        ]);

        // condition
        // each accident has price 10
        factory(FinanceCondition::class)->create([
            'type' => 'add',
            'value' => '10',
            'currency_id' => $currency->id,
            'currency_mode' => 'currency',
            'model' => Assistant::class,
        ]);

        $response = $this->sendGet('/api/director/cases/'.$accident->id.'/finance');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'calculatedValue' => 0,
                    'formula' => '5.00 - 5.00',
                    'payment' => [
                        'id' => $incomePayment->id,
                    ]
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'calculatedValue' => 0,
                    'formula' => 'invoice',
                    'payment' => [
                        'id' => $invoice->payment->id,
                        'value' => $invoice->payment->value
                    ],
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'payment' => [
                        'id' => $caseablePayment->id,
                    ],
                    'formula' => 'fixed',
                    'calculatedValue' => 0,
                ],
            ],
        ]);
    }

    /**
     * Conditions of the Assistant - Conditions of the caseable
     */
    public function testIncomeFromCounted(): void
    {
        $caseable = factory(HospitalAccident::class)->create();
        $currency = factory(FinanceCurrency::class)->create();
        $caseablePayment = factory(Payment::class)->create([
            'currency_id' => $currency->id,
            'fixed' => 0,
            'value' => 5
        ]);
        $incomePayment = factory(Payment::class)->create([
            'currency_id' => $currency->id,
            'fixed' => 0,
            'value' => 700,
        ]);
        $assistantPayment = factory(Payment::class)->create([
            'currency_id' => $currency->id,
            'fixed' => 0,
            'value' => 20,
        ]);
        $accident = $accident = $this->createNewAccident([
            'caseable_type' => HospitalAccident::class,
            'caseable_id' => $caseable->id,
            'caseable_payment_id' => $caseablePayment->id,
            'income_payment_id' => $incomePayment->id,
            'assistant_payment_id' => $assistantPayment->id,
            'accident_status_id' => 0,
        ]);

        // condition
        // each accident has price 10
        factory(FinanceCondition::class)->create([
            'type' => 'add',
            'value' => '10',
            'currency_id' => $currency->id,
            'currency_mode' => 'currency',
            'model' => Assistant::class,
        ]);

        $response = $this->sendGet('/api/director/cases/'.$accident->id.'/finance');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'calculatedValue' => 10,
                    'formula' => '10.00 - 0.00',
                    'payment' => [
                        'id' => $incomePayment->id,
                    ]
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'calculatedValue' => 10,
                    'formula' => '10.00',
                    'payment' => ['id' => $assistantPayment->id],
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'payment' => [
                        'id' => $caseablePayment->id,
                    ],
                    'formula' => '0.00',
                    'calculatedValue' => 0,
                ],
            ],
        ]);
    }
}
