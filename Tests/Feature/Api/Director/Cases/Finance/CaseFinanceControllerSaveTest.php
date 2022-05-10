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

namespace medcenter24\mcCore\Tests\Feature\Api\Director\Cases\Finance;

use Illuminate\Support\Facades\Event;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Events\Accident\Payment\AccidentPaymentChangedEvent;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\CurrencyService;
use medcenter24\mcCore\App\Services\Entity\PaymentService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class CaseFinanceControllerSaveTest extends TestCase
{
    use DirectorTestTraitApi;

    private CurrencyService $currencyService;
    private AccidentService $accidentService;
    private PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currencyService = new CurrencyService();
        $this->paymentService = new PaymentService();
        $this->accidentService = new AccidentService();
    }

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
                    'cashPayment' => [
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
                            'value' => 2,
                            'currencyId' => 1,
                            'fixed' => true,
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
                            'value' => 1,
                            'currencyId' => 1,
                            'fixed' => true,
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
                            'value' => 1,
                            'currencyId' => 1,
                            'fixed' => true,
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
        Event::fake([
            AccidentPaymentChangedEvent::class,
        ]);

        $this->currencyService->create();
        /** @var Accident $accident */
        $accident = $this->accidentService->create($accidentData);

        $incomePayment = $this->paymentService->create($paymentData['incomePayment']);
        $caseablePayment = $this->paymentService->create($paymentData['caseablePayment']);
        $assistantPayment = $this->paymentService->create($paymentData['assistantPayment']);
        $cashPayment = $this->paymentService->create($paymentData['cashPayment']);

        $accident->incomePayment()->associate($incomePayment);
        $accident->paymentToCaseable()->associate($caseablePayment);
        $accident->paymentFromAssistant()->associate($assistantPayment);
        $accident->cashPayment()->associate($cashPayment);
        $accident->save();

        // write data
        foreach ($toSavePaymentData as $key => $val) {
            $response = $this->sendPut('/api/director/cases/'.$accident->id.'/finance/'.$key, $val);
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
    }
}
