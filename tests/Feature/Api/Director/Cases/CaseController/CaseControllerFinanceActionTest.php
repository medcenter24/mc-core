<?php
/**
 * Copyright (c) 2019.
 *
 * Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director\Cases\CaseController;

use App\Accident;
use App\Assistant;
use App\City;
use App\Doctor;
use App\FinanceCondition;
use App\FinanceCurrency;
use App\FinanceStorage;
use App\Hospital;
use App\HospitalAccident;
use App\Invoice;
use App\Payment;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;

class CaseControllerFinanceActionTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function test404(): void {
        $response = $this->json('POST', '/api/director/cases/1/finance', [], $this->headers($this->getUser()));
        $response->assertStatus(404);
        $response->assertJson([]);
    }

    public function testWithoutCondition(): void {
        $accident = factory(Accident::class)->create();
        factory(FinanceCurrency::class)->create();
        $response = $this->json('POST', '/api/director/cases/'.$accident->id.'/finance', [], $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'value' => 0,
                    'currency' => [],
                    'formula' => '0.00 - 0.00',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'value' => 0,
                    'currency' => [],
                    'formula' => '0.00',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'value' => 0,
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
        $accident = factory(Accident::class)->create();
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

        $response = $this->json('POST', '/api/director/cases/'.$accident->id.'/finance', [], $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'value' => 10,
                    'currency' => [],
                    'formula' => '10.00 - 0.00',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'value' => 10,
                    'currency' => [],
                    'formula' => '10.00',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'value' => 0,
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
        $accident = factory(Accident::class)->create([
            'city_id' => $city->id,
        ]);
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

        $response = $this->json('POST', '/api/director/cases/'.$accident->id.'/finance', [], $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'value' => 16.83,
                    'currency' => [],
                    'formula' => '16.83 - 0.00',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'value' => 16.83,
                    'currency' => [],
                    'formula' => '( 10.00 + 7.00 ) * 99%',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'value' => 0,
                    'currency' => [],
                    'formula' => '0.00',
                ],
            ],
        ]);
    }

    public function testDoctorCondition(): void
    {
        $accident = factory(Accident::class)->create();
        factory(FinanceCurrency::class)->create();

        // condition
        // each accident has price 10
        factory(FinanceCondition::class)->create([
            'type' => 'add',
            'value' => '10',
            'currency_id' => 1,
            'currency_mode' => 'currency',
            'model' => Doctor::class,
        ]);

        $response = $this->json('POST', '/api/director/cases/'.$accident->id.'/finance', [], $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'value' => -10,
                    'currency' => [],
                    'formula' => '0.00 - 10.00',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'value' => 0,
                    'currency' => [],
                    'formula' => '0.00',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'value' => 10,
                    'currency' => [],
                    'formula' => '10.00',
                ],
            ],
        ]);
    }

    /**
     * Both doctors and assistant conditions
     */
    public function testMixedConditions(): void
    {
        $accident = factory(Accident::class)->create();
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

        $response = $this->json('POST', '/api/director/cases/'.$accident->id.'/finance', [], $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'value' => 1659.81,
                    'currency' => [],
                    'formula' => '3806.00 - 2146.19',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'value' => 3806.0000000000005,
                    'currency' => [],
                    'formula' => '( 10.00 + 3450.00 ) * 110%',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'value' => 2146.1895,
                    'currency' => [],
                    'formula' => '( 4.99 + 2039.00 ) * 105%',
                ],
            ],
        ]);
    }

    public function testMixedComplexCondition(): void
    {
        $accident = factory(Accident::class)->create();
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
        factory(FinanceCondition::class)->create([
            'type' => 'add',
            'value' => '4.991',
            'currency_id' => 1,
            'currency_mode' => 'currency',
            'model' => Doctor::class,
        ]);

        $response = $this->json('POST', '/api/director/cases/'.$accident->id.'/finance', [], $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'value' => 5.01,
                    'currency' => [],
                    'formula' => '10.00 - 4.99',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'value' => 10,
                    'currency' => [],
                    'formula' => '10.00',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'value' => 4.99,
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
        $accident = factory(Accident::class)->create([
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

        $response = $this->json('POST', '/api/director/cases/'.$accident->id.'/finance', [], $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'value' => -10,
                    'currency' => [],
                    'formula' => '0.00 - 10.00',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'value' => 0,
                    'currency' => [],
                    'formula' => '0.00',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'value' => 10,
                    'currency' => [],
                    'formula' => '10.00',
                ],
            ],
        ]);
    }

    /**
     * Checks that Hospital prices are taken from the invoices from the hospital or stored as a fixed payment
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
        $accident = factory(Accident::class)->create([
            'caseable_type' => HospitalAccident::class,
            'caseable_id' => $caseable->id,
            'assistant_invoice_id' => $invoice->id,
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

        $response = $this->json('POST', '/api/director/cases/'.$accident->id.'/finance', [], $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'value' => -15,
                    'currency' => [],
                    'formula' => '0.00 - 15.00',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'value' => 0,
                    'currency' => [],
                    'formula' => '0.00',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'value' => 15,
                    'currency' => [],
                    'formula' => '10.00 + 5.00',
                ],
            ],
        ]);
    }

    /**
     * Checks that Hospital prices are taken from the invoices from the hospital or stored as a fixed payment
     */
    public function testHospitalFixedPayment(): void
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
        $accident = factory(Accident::class)->create([
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

        $response = $this->json('POST', '/api/director/cases/'.$accident->id.'/finance', [], $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'income',
                    'loading' => false,
                    'value' => 2,
                    'currency' => [],
                    'formula' => '0.00 - -2.00',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'value' => 0,
                    'currency' => [],
                    'formula' => '0.00',
                ],
                [
                    'type' => 'caseable',
                    'loading' => false,
                    'value' => -2,
                    'currency' => [],
                    'formula' => '-2.00',
                ],
            ],
        ]);
    }
}
