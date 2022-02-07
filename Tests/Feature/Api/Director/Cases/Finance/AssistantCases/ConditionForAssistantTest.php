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

use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\City;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\CityService;
use medcenter24\mcCore\App\Services\Entity\CurrencyService;
use medcenter24\mcCore\App\Services\Entity\FinanceConditionService;
use medcenter24\mcCore\App\Services\Entity\FinanceStorageService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class ConditionForAssistantTest extends TestCase
{
    use DirectorTestTraitApi;

    public function setUp(): void
    {
        parent::setUp();

        $this->accidentService = new AccidentService();
        $this->financeConditionService = new FinanceConditionService();
        $this->currencyService = new CurrencyService();

        $this->cityService = new CityService();
        $this->financeStorageService = new FinanceStorageService();

        // one fake currency for storage
        $this->currencyService->create();
    }

    public function testStoredAssistantCondition(): void
    {
        $city = $this->cityService->create();
        $city2 = $this->cityService->create();
        $accident = $this->accidentService->create(['city_id' => $city->id]);
        $currency = $this->currencyService->create();

        // condition
        // each accident has price 10
        $this->financeConditionService->create([
            'title' => 'test',
            'type' => 'add',
            'value' => '10',
            'currency_id' => $currency->id,
            'currency_mode' => 'currency',
            'model' => Assistant::class,
        ]);

        // condition for the city
        $this->financeStorageService->create([
            'finance_condition_id' => $this->financeConditionService->create([
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
        $this->financeStorageService->create([
            'finance_condition_id' => $this->financeConditionService->create([
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
        $this->financeStorageService->create([
            'finance_condition_id' => $this->financeConditionService->create([
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

    /**
     * Base price should be defined only once, other prices will be added or substituted to it
     * @return void
     */
    public function testBasePrice(): void
    {

    }
}
