<?php
/**
 * Copyright (c) 2019.
 *
 * Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director\Cases\CaseController\Finance;

use App\Accident;
use App\Assistant;
use App\Doctor;
use App\DoctorAccident;
use App\DoctorService;
use App\FinanceCondition;
use App\FinanceCurrency;
use App\FinanceStorage;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DoctorCaseTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [

            // TEST 1

            /** +10 for accident - (4.99 for the case to doctor + 1 for the service to doctor) */
            [
                // accident
                [
                    'accidentData' => [],
                    // caseable
                    'caseable' => [
                        'caseableData' => [],
                        // doctor
                        'doctor' => [],
                        // services
                        'services' => [
                            [],
                        ],
                    ],
                ],
                // currency
                [],
                // conditions
                [
                    // each accident has price 10
                    [
                        'conditionData' => [
                            'type' => 'add',
                            'value' => '10',
                            'currency_mode' => 'currency',
                            'model' => Assistant::class,
                        ],
                    ],
                    // only defined doctor will be paid for 4.99
                    [
                        'conditionData' => [
                            'type' => 'add',
                            'value' => '4.991',
                            'currency_mode' => 'currency',
                            'model' => Doctor::class,
                        ],
                        // stored rules
                        'storageRules' => [
                            [
                                'setCurrentDoctor' => 1,
                                'model' => Doctor::class,
                            ],
                        ],
                    ],
                    // only defined service will be paid for 1
                    [
                        'conditionData' => [
                            'type' => 'add',
                            'value' => '1',
                            'currency_mode' => 'currency',
                            'model' => Doctor::class,
                        ],
                        // stored rules
                        'storageRules' => [
                            [
                                'setExistingService' => 1,
                                'model' => DoctorService::class,
                            ],
                        ],
                    ],
                ],
                // result
                [
                    'income' => [
                        'type'  => 'income',
                        'loading' => false,
                        'calculatedValue' => 4.01,
                        'currency' => [],
                        'formula' => '10.00 - 5.99',
                    ],
                    'assistant' => [
                        'type' => 'assistant',
                        'loading' => false,
                        'calculatedValue' => 10,
                        'currency' => [],
                        'formula' => '10.00',
                    ],
                    'caseable' => [
                        'type' => 'caseable',
                        'loading' => false,
                        'calculatedValue' => 5.99,
                        'currency' => [],
                        'formula' => '4.99 + 1.00',
                    ],
                ]
            ],

        ];
    }

    /**
     * A basic test example.
     *
     * @param array $accidentData
     * @param array $currencyData
     * @param array $conditionsData
     * @param array $results
     *
     * @dataProvider dataProvider
     * @return void
     */
    public function testCases(array $accidentData, array $currencyData, array $conditionsData, array $results): void
    {
        $doctor = factory(Doctor::class)->create($accidentData['caseable']['doctor']);
        $caseable = factory(DoctorAccident::class)->create(
            array_merge($accidentData['caseable']['caseableData'], [
                'doctor_id' => $doctor->id,
            ])
        );

        $services = [];
        if (count($accidentData['caseable']['services'])) {
            foreach ($accidentData['caseable']['services'] as $serviceData) {
                $services[] = factory(DoctorService::class)->create($serviceData)->id;
            }

            $caseable->services()->attach($services);
        }

        $accident = factory(Accident::class)->create(
            array_merge($accidentData['accidentData'], [
                'caseable_type' => DoctorAccident::class,
                'caseable_id' => $caseable->id,
            ])
        );
        $currency = factory(FinanceCurrency::class)->create($currencyData);

        foreach ($conditionsData as $conditionData) {
            $condition = factory(FinanceCondition::class)->create(
                array_merge($conditionData['conditionData'], ['currency_id' => $currency->id])
            );

            if (array_key_exists('storageRules', $conditionsData)) {
                foreach ($conditionsData['storageRules'] as $storedRule) {
                    $buff = $storedRule;
                    $buff['finance_condition_id'] = $condition->id;
                    if (array_key_exists('setCurrentDoctor', $buff)) {
                        unset($buff['setCurrentDoctor']);
                        $buff['model_id'] = $doctor->id;
                    }
                    if (array_key_exists('setExistingService', $buff) && isset($services)) {
                        unset($buff['setExistingService']);
                        $buff['model_id'] = current($services);
                    }
                    factory(FinanceStorage::class)->create($storedRule);
                }
            }
        }

        $response = $this->json('POST', '/api/director/cases/'.$accident->id.'/finance', [], $this->headers($this->getUser()));
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
