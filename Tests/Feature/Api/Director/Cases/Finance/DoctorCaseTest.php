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

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\App\Entity\FinanceCondition;
use medcenter24\mcCore\App\Entity\FinanceCurrency;
use medcenter24\mcCore\App\Entity\FinanceStorage;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class DoctorCaseTest extends TestCase
{
    use DirectorTestTraitApi;

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
                                'model' => Service::class,
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
                        'formula' => '10.00 + 0.00 - 5.99',
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
        $doctor = Doctor::factory()->create($accidentData['caseable']['doctor']);
        $caseable = DoctorAccident::factory()->create(
            array_merge($accidentData['caseable']['caseableData'], [
                'doctor_id' => $doctor->id,
            ])
        );

        $services = [];
        if (count($accidentData['caseable']['services'])) {
            foreach ($accidentData['caseable']['services'] as $serviceData) {
                $services[] = Service::factory()->create($serviceData)->id;
            }

            $caseable->services()->attach($services);
        }

        $accident = Accident::factory()->create(
            array_merge($accidentData['accidentData'], [
                'caseable_type' => DoctorAccident::class,
                'caseable_id' => $caseable->id,
            ])
        );
        $currency = FinanceCurrency::factory()->create($currencyData);

        foreach ($conditionsData as $conditionData) {
            $condition = FinanceCondition::factory()->create(
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
                    FinanceStorage::factory()->create($storedRule);
                }
            }
        }

        $response = $this->sendGet('/api/director/cases/'.$accident->id.'/finance');
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
