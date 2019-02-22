<?php
/**
 * Copyright (c) 2019.
 *
 * Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director\Cases\CaseController\Finance;

use App\Accident;
use App\FinanceCurrency;
use App\Payment;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;
use Tests\TestCase;

class CaseFinanceControllerSaveTest extends TestCase
{

    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function dataProvider(): array
    {
        return [
            [
                // accident data
                [],
                // payments data
                [
                    'incomePayment' => [
                        'value' => 1,
                        'currency_id' => 1,
                        'fixed' => 1,
                    ],
                    'caseablePayment' => [
                        'value' => 1,
                        'currency_id' => 1,
                        'fixed' => 1,
                    ],
                    'assistantPayment' => [
                        'value' => 1,
                        'currency_id' => 1,
                        'fixed' => 1,
                    ],
                ],
                // new data for rewriting
                [
                    'income' => [
                        'fixed' => 1,
                        'price' => 2,
                    ],
                ],
                // results
                [
                    'income' => [
                        'calculatedValue' => 0,
                        'currency' => [
                            'id' => 1,
                        ],
                        'formula' => 'fixed',
                        'loading' => false,
                        'payment' => [
                            'value' => '2',
                            'currency_id' => '1',
                            'fixed' => '1',
                        ],
                        'type' => 'income',
                    ],
                    'caseable' => [
                        'calculatedValue' => 0,
                        'currency' => [
                            'id' => 1,
                        ],
                        'formula' => 'fixed',
                        'loading' => false,
                        'payment' => [
                            'value' => '1',
                            'currency_id' => '1',
                            'fixed' => '1',
                        ],
                        'type' => 'caseable',
                    ],
                    'assistant' => [
                        'calculatedValue' => 0,
                        'currency' => [
                            'id' => 1,
                        ],
                        'formula' => 'fixed',
                        'loading' => false,
                        'payment' => [
                            'value' => '1',
                            'currency_id' => '1',
                            'fixed' => '1',
                        ],
                        'type' => 'assistant',
                    ],
                ],
            ]
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param array $accidentData
     * @param array $paymentData
     * @param array $toSavePaymentData
     * @param array $results
     */
    public function testSave(array $accidentData, array $paymentData, array $toSavePaymentData, array $results): void
    {
        factory(FinanceCurrency::class)->create();
        /** @var Accident $accident */
        $accident = factory(Accident::class)->create($accidentData);

        $incomePayment = factory(Payment::class)->create($paymentData['incomePayment']);
        $caseablePayment = factory(Payment::class)->create($paymentData['caseablePayment']);
        $assistantPayment = factory(Payment::class)->create($paymentData['assistantPayment']);

        $accident->incomePayment()->associate($incomePayment);
        $accident->paymentToCaseable()->associate($caseablePayment);
        $accident->paymentFromAssistant()->associate($assistantPayment);
        $accident->save();

        // write data
        foreach ($toSavePaymentData as $key => $val) {
            $response = $this->json('PUT', '/api/director/cases/'.$accident->id.'/finance/'.$key, $val, $this->headers($this->getUser()));
        }
        if (isset($response)) {
            $response->assertStatus(200);
            $response->assertJson([
                'data' => [
                    $results['income'],
                    $results['assistant'],
                    $results['caseable'],
                ],
            ]);
        }
        // check rewritten data
        /*$response = $this->json('POST', '/api/director/cases/'.$accident->id.'/finance', [], $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                $results['income'],
                $results['assistant'],
                $results['caseable'],
            ],
        ]);*/
    }
}
