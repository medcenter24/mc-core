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
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\CurrencyService;
use medcenter24\mcCore\App\Services\Entity\FinanceConditionService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class GeneralPaymentForAllAssistantsTestCase extends TestCase
{
    use DirectorTestTraitApi;

    public function setUp(): void
    {
        parent::setUp();

        $this->accidentService = new AccidentService();
        $this->financeConditionService = new FinanceConditionService();
        $this->currencyService = new CurrencyService();

        // one fake currency for storage
        $this->currencyService->create();
    }

    /**
     * Global condition for each accident price that Assistant should pay
     * each accident has price 10 for all Assistants
     */
    public function testGeneralCondition(): void
    {
        $accident = $this->accidentService->create();

        $this->financeConditionService->create([
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
     * Global condition for each accident price that Assistant should pay
     * each accident has price 10 + 5 - 2 - 1 for all Assistants
     */
    public function testManyGeneralConditions(): void
    {
        $accident = $this->accidentService->create();

        $this->financeConditionService->create([
            'type' => 'add',
            'value' => '10',
            'currency_id' => 1,
            'currency_mode' => 'currency',
            'model' => Assistant::class,
        ]);

        $this->financeConditionService->create([
            'type' => 'add',
            'value' => '5',
            'currency_id' => 1,
            'currency_mode' => 'currency',
            'model' => Assistant::class,
        ]);

        $this->financeConditionService->create([
            'type' => 'sub',
            'value' => '2',
            'currency_id' => 1,
            'currency_mode' => 'currency',
            'model' => Assistant::class,
        ]);

        $this->financeConditionService->create([
            'type' => 'sub',
            'value' => '1',
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
                    'calculatedValue' => 12,
                    'currency' => [],
                    'formula' => '12.00 - 0.00',
                ],
                [
                    'type' => 'assistant',
                    'loading' => false,
                    'calculatedValue' => 12,
                    'currency' => [],
                    'formula' => '10.00 + 5.00 - 2.00 - 1.00',
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
}
