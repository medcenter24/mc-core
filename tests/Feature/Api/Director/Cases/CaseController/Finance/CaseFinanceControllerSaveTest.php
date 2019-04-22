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

namespace medcenter24\mcCore\Tests\Feature\Api\Director\Cases\CaseController\Finance;

use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\FinanceCurrency;
use medcenter24\mcCore\App\Payment;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use medcenter24\mcCore\Tests\Feature\Api\JwtHeaders;
use medcenter24\mcCore\Tests\Feature\Api\LoggedUser;
use medcenter24\mcCore\Tests\TestCase;

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
