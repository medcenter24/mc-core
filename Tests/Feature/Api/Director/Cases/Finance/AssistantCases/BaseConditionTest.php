<?php
/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2022 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace Api\Director\Cases\Finance\AssistantCases;

use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\City;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AssistantService;
use medcenter24\mcCore\App\Services\Entity\CityService;
use medcenter24\mcCore\App\Services\Entity\CurrencyService;
use medcenter24\mcCore\App\Services\Entity\FinanceConditionService;
use medcenter24\mcCore\App\Services\Entity\FinanceStorageService;
use medcenter24\mcCore\App\Services\Entity\ServiceService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class BaseConditionTest extends TestCase
{
    use DirectorTestTraitApi;

    private Model $currency;
    private AssistantService $assistantService;
    private ServiceService $serviceService;
    private CityService $cityService;
    private FinanceConditionService $financeConditionService;
    private AccidentService $accidentService;
    private FinanceStorageService $financeStorageService;

    public function setUp(): void
    {
        parent::setUp();

        $this->accidentService = new AccidentService();
        $this->financeConditionService = new FinanceConditionService();
        $currencyService = new CurrencyService();

        $this->cityService = new CityService();
        $this->serviceService = new ServiceService();
        $this->assistantService = new AssistantService();

        $this->financeStorageService = new FinanceStorageService();

        // one fake currency for storage
        $this->currency = $currencyService->create();
    }

    public function testStoreBaseCondition(): void
    {
        $this->financeConditionService->create([
            'title'         => 'test',
            'type'          => 'base',
            'value'         => '10',
            'currency_id'   => $this->currency->getAttribute('id'),
            'currency_mode' => 'currency',
            'model'         => Assistant::class,
        ]);

        $accident = $this->accidentService->create();

        $response = $this->sendGet('/api/director/cases/' . $accident->id . '/finance');
        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    [
                        'type'            => 'income',
                        'loading'         => false,
                        'payment'         => null,
                        'currency'        => [
                            'id'    => 2,
                            'title' => 'Euro',
                            'code'  => 'eu',
                            'ico'   => 'fa fa-euro',
                        ],
                        'formula'         => '10.00 + 0.00 - 0.00',
                        'calculatedValue' => 10,
                    ],
                    [
                        'type'            => 'assistant',
                        'loading'         => false,
                        'payment'         => null,
                        'currency'        => [
                            'id'    => 2,
                            'title' => 'Euro',
                            'code'  => 'eu',
                            'ico'   => 'fa fa-euro',
                        ],
                        'formula'         => '10.00',
                        'calculatedValue' => 10,
                    ],
                    [
                        'type'            => 'caseable',
                        'loading'         => false,
                        'payment'         => null,
                        'currency'        => [
                            'id'    => 2,
                            'title' => 'Euro',
                            'code'  => 'eu',
                            'ico'   => 'fa fa-euro',
                        ],
                        'formula'         => 0,
                        'calculatedValue' => 0,
                    ],
                ],
            ]
        );
    }

    public function testStoreTwoBaseConditions(): void
    {
        $this->financeConditionService->create([
            'title'         => 'test',
            'type'          => 'base',
            'value'         => '10',
            'currency_id'   => $this->currency->getAttribute('id'),
            'currency_mode' => 'currency',
            'model'         => Assistant::class,
        ]);

        $this->financeConditionService->create([
            'title'         => 'test',
            'type'          => 'base',
            'value'         => '11',
            'currency_id'   => $this->currency->getAttribute('id'),
            'currency_mode' => 'currency',
            'model'         => Assistant::class,
        ]);

        $accident = $this->accidentService->create();

        $response = $this->sendGet('/api/director/cases/' . $accident->id . '/finance');
        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    [
                        'type'            => 'income',
                        'loading'         => false,
                        'payment'         => null,
                        'currency'        => [
                            'id'    => 2,
                            'title' => 'Euro',
                            'code'  => 'eu',
                            'ico'   => 'fa fa-euro',
                        ],
                        'formula'         => '11.00 + 0.00 - 0.00',
                        'calculatedValue' => 11,
                    ],
                    [
                        'type'            => 'assistant',
                        'loading'         => false,
                        'payment'         => null,
                        'currency'        => [
                            'id'    => 2,
                            'title' => 'Euro',
                            'code'  => 'eu',
                            'ico'   => 'fa fa-euro',
                        ],
                        'formula'         => '11.00',
                        'calculatedValue' => 11,
                    ],
                    [
                        'type'            => 'caseable',
                        'loading'         => false,
                        'payment'         => null,
                        'currency'        => [
                            'id'    => 2,
                            'title' => 'Euro',
                            'code'  => 'eu',
                            'ico'   => 'fa fa-euro',
                        ],
                        'formula'         => 0,
                        'calculatedValue' => 0,
                    ],
                    [
                        'type' => 'cash',
                        'loading' => false,
                        'payment' => NULL,
                        'currency' =>
                            array (
                                'id' => 2,
                                'title' => 'Euro',
                                'code' => 'eu',
                                'ico' => 'fa fa-euro',
                            ),
                        'formula' => '',
                        'calculatedValue' => 0,
                    ]
                ],
            ]
        );
    }

    public function testSelectBaseConditionByCityAssigned(): void
    {
        $city = $this->cityService->create();

        $condition1 = $this->financeConditionService->create([
            'title'         => 'test',
            'type'          => 'base',
            'value'         => '10',
            'currency_id'   => $this->currency->getAttribute('id'),
            'currency_mode' => 'currency',
            'model'         => Assistant::class,
        ]);

        $this->financeConditionService->create([
            'title'         => 'test',
            'type'          => 'base',
            'value'         => '11',
            'currency_id'   => $this->currency->getAttribute('id'),
            'currency_mode' => 'currency',
            'model'         => Assistant::class,
        ]);

        // condition for the city
        $this->financeStorageService->create([
            'finance_condition_id' => $condition1->getAttribute('id'),
            'model'                => City::class,
            'model_id'             => $city->getAttribute('id'),
        ]);

        $accident = $this->accidentService->create(['city_id' => $city->id]);

        $response = $this->sendGet('/api/director/cases/' . $accident->id . '/finance');
        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    [
                        'type'            => 'income',
                        'loading'         => false,
                        'payment'         => null,
                        'currency'        => [
                            'id'    => 2,
                            'title' => 'Euro',
                            'code'  => 'eu',
                            'ico'   => 'fa fa-euro',
                        ],
                        'formula'         => '10.00 + 0.00 - 0.00',
                        'calculatedValue' => 10,
                    ],
                    [
                        'type'            => 'assistant',
                        'loading'         => false,
                        'payment'         => null,
                        'currency'        => [
                            'id'    => 2,
                            'title' => 'Euro',
                            'code'  => 'eu',
                            'ico'   => 'fa fa-euro',
                        ],
                        'formula'         => '10.00',
                        'calculatedValue' => 10,
                    ],
                    [
                        'type'            => 'caseable',
                        'loading'         => false,
                        'payment'         => null,
                        'currency'        => [
                            'id'    => 2,
                            'title' => 'Euro',
                            'code'  => 'eu',
                            'ico'   => 'fa fa-euro',
                        ],
                        'formula'         => 0,
                        'calculatedValue' => 0,
                    ],
                    [
                        'type' => 'cash',
                        'loading' => false,
                        'payment' => NULL,
                        'currency' =>
                            array (
                                'id' => 2,
                                'title' => 'Euro',
                                'code' => 'eu',
                                'ico' => 'fa fa-euro',
                            ),
                        'formula' => '',
                        'calculatedValue' => 0,
                    ]
                ],
            ]
        );
    }

    public function testSelectManyBaseConditionByCityAssigned(): void
    {
        $city = $this->cityService->create();

        $condition1 = $this->financeConditionService->create([
            'title'         => 'test',
            'type'          => 'base',
            'value'         => '10',
            'currency_id'   => $this->currency->getAttribute('id'),
            'currency_mode' => 'currency',
            'model'         => Assistant::class,
        ]);

        // condition for the city
        $this->financeStorageService->create([
            'finance_condition_id' => $condition1->getAttribute('id'),
            'model'                => City::class,
            'model_id'             => $city->getAttribute('id'),
        ]);

        $condition2 = $this->financeConditionService->create([
            'title'         => 'test',
            'type'          => 'base',
            'value'         => '10.1',
            'currency_id'   => $this->currency->getAttribute('id'),
            'currency_mode' => 'currency',
            'model'         => Assistant::class,
        ]);

        // condition for the city
        $this->financeStorageService->create([
            'finance_condition_id' => $condition2->getAttribute('id'),
            'model'                => City::class,
            'model_id'             => $city->getAttribute('id'),
        ]);

        $this->financeConditionService->create([
            'title'         => 'test',
            'type'          => 'base',
            'value'         => '11',
            'currency_id'   => $this->currency->getAttribute('id'),
            'currency_mode' => 'currency',
            'model'         => Assistant::class,
        ]);

        $accident = $this->accidentService->create(['city_id' => $city->id]);

        $response = $this->sendGet('/api/director/cases/' . $accident->id . '/finance');
        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    [
                        'type'            => 'income',
                        'loading'         => false,
                        'payment'         => null,
                        'currency'        => [
                            'id'    => 2,
                            'title' => 'Euro',
                            'code'  => 'eu',
                            'ico'   => 'fa fa-euro',
                        ],
                        'formula'         => '10.10 + 0.00 - 0.00',
                        'calculatedValue' => 10.1,
                    ],
                    [
                        'type'            => 'assistant',
                        'loading'         => false,
                        'payment'         => null,
                        'currency'        => [
                            'id'    => 2,
                            'title' => 'Euro',
                            'code'  => 'eu',
                            'ico'   => 'fa fa-euro',
                        ],
                        'formula'         => '10.10',
                        'calculatedValue' => 10.1,
                    ],
                    [
                        'type'            => 'caseable',
                        'loading'         => false,
                        'payment'         => null,
                        'currency'        => [
                            'id'    => 2,
                            'title' => 'Euro',
                            'code'  => 'eu',
                            'ico'   => 'fa fa-euro',
                        ],
                        'formula'         => 0,
                        'calculatedValue' => 0,
                    ],
                    [
                        'type' => 'cash',
                        'loading' => false,
                        'payment' => NULL,
                        'currency' =>
                            array (
                                'id' => 2,
                                'title' => 'Euro',
                                'code' => 'eu',
                                'ico' => 'fa fa-euro',
                            ),
                        'formula' => '',
                        'calculatedValue' => 0,
                    ]
                ],
            ]
        );
    }

    public function testSelectManyBaseConditionsAssigned(): void
    {
        $city = $this->cityService->create();

        $condition1 = $this->financeConditionService->create([
            'title'         => 'test',
            'type'          => 'base',
            'value'         => '10',
            'currency_id'   => $this->currency->getAttribute('id'),
            'currency_mode' => 'currency',
            'model'         => Assistant::class,
        ]);

        // condition for the city
        $this->financeStorageService->create([
            'finance_condition_id' => $condition1->getAttribute('id'),
            'model'                => City::class,
            'model_id'             => $city->getAttribute('id'),
        ]);

        // condition for the services
        $service1 = $this->serviceService->create();
        $this->financeStorageService->create([
            'finance_condition_id' => $condition1->getAttribute('id'),
            'model'                => Service::class,
            'model_id'             => $service1,
        ]);

        $service2 = $this->serviceService->create();
        $this->financeStorageService->create([
            'finance_condition_id' => $condition1->getAttribute('id'),
            'model'                => Service::class,
            'model_id'             => $service2,
        ]);

        $condition2 = $this->financeConditionService->create([
            'title'         => 'test',
            'type'          => 'base',
            'value'         => '10.1',
            'currency_id'   => $this->currency->getAttribute('id'),
            'currency_mode' => 'currency',
            'model'         => Assistant::class,
        ]);

        $this->financeStorageService->create([
            'finance_condition_id' => $condition2->getAttribute('id'),
            'model'                => Service::class,
            'model_id'             => $service2,
        ]);

        // condition for the city
        $this->financeStorageService->create([
            'finance_condition_id' => $condition2->getAttribute('id'),
            'model'                => City::class,
            'model_id'             => $city->getAttribute('id'),
        ]);

        $this->financeConditionService->create([
            'title'         => 'test',
            'type'          => 'base',
            'value'         => '11',
            'currency_id'   => $this->currency->getAttribute('id'),
            'currency_mode' => 'currency',
            'model'         => Assistant::class,
        ]);

        $assistant = $this->assistantService->create();
        // condition for the city
        $this->financeStorageService->create([
            'finance_condition_id' => $condition1->getAttribute('id'),
            'model'                => Assistant::class,
            'model_id'             => $city->getAttribute('id'),
        ]);

        $caseable = DoctorAccident::factory()->create();
        $accident = $this->accidentService->create([
            'city_id'       => $city->getAttribute('id'),
            'caseable_type' => DoctorAccident::class,
            'caseable_id'   => $caseable->getAttribute('id'),
            'assistant_id'  => $assistant->getAttribute('id'),
        ]);
        $accident->getAttribute('caseable')->services()->attach($service2);

        $response = $this->sendGet('/api/director/cases/' . $accident->getAttribute('id') . '/finance');
        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    [
                        'type'            => 'income',
                        'loading'         => false,
                        'payment'         => null,
                        'currency'        => [
                            'id'    => 2,
                            'title' => 'Euro',
                            'code'  => 'eu',
                            'ico'   => 'fa fa-euro',
                        ],
                        'formula'         => '10.00 + 0.00 - 0.00',
                        'calculatedValue' => 10,
                    ],
                    [
                        'type'            => 'assistant',
                        'loading'         => false,
                        'payment'         => null,
                        'currency'        => [
                            'id'    => 2,
                            'title' => 'Euro',
                            'code'  => 'eu',
                            'ico'   => 'fa fa-euro',
                        ],
                        'formula'         => '10.00',
                        'calculatedValue' => 10,
                    ],
                    [
                        'type'            => 'caseable',
                        'loading'         => false,
                        'payment'         => null,
                        'currency'        => [
                            'id'    => 2,
                            'title' => 'Euro',
                            'code'  => 'eu',
                            'ico'   => 'fa fa-euro',
                        ],
                        'formula'         => 0,
                        'calculatedValue' => 0,
                    ],
                    [
                        'type' => 'cash',
                        'loading' => false,
                        'payment' => NULL,
                        'currency' =>
                            array (
                                'id' => 2,
                                'title' => 'Euro',
                                'code' => 'eu',
                                'ico' => 'fa fa-euro',
                            ),
                        'formula' => '',
                        'calculatedValue' => 0,
                    ]
                ],
            ]
        );
    }
}
