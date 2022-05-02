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

declare(strict_types=1);

namespace medcenter24\mcCore\Tests\Feature\Api\Director;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\FinanceCondition;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;
use medcenter24\mcCore\Tests\Unit\fakes\DoctorServiceFake;
use medcenter24\mcCore\Tests\Unit\fakes\AssistantFake;
use medcenter24\mcCore\Tests\Unit\fakes\CityFake;
use medcenter24\mcCore\Tests\Unit\fakes\DatePeriodFake;
use medcenter24\mcCore\Tests\Unit\fakes\DoctorFake;

class FinanceConditionControllerTest extends TestCase
{
    use DirectorTestTraitApi;

    private const URI = '/api/director/finance';

    public function testStoreError(): void
    {
        $newFinanceCondition = [];
        $this->doNotPrintErrResponse([422]);
        $response = $this->sendPost(self::URI, $newFinanceCondition);
        $this->doNotPrintErrResponse();
        $response->assertStatus(422);
        self::assertArrayHasKey('title', $response->json('errors'), 'Error with `title` message exists');
        self::assertArrayHasKey('value', $response->json('errors'), 'Error with `value` message exists');
        self::assertArrayHasKey('currencyMode', $response->json('errors'), 'Error with `currencyMode` message exists');
    }

    public function testIndex(): void
    {
        $newFinanceCondition = [
            'title' => 'feature_test',
            'value' => 50,
            'currencyMode' => 'percent',
            'order' => 7,
        ];
        $this->sendPost(self::URI, $newFinanceCondition);
        $newFinanceCondition = [
            'title' => 'feature_test',
            'value' => 50,
            'currencyMode' => 'percent',
            'order' => 17,
        ];
        $this->sendPost(self::URI, $newFinanceCondition);
        $newFinanceCondition = [
            'title' => 'feature_test',
            'value' => 50,
            'currencyMode' => 'percent',
            'order' => 27,
        ];
        $this->sendPost(self::URI, $newFinanceCondition);
        $newFinanceCondition = [
            'title' => 'feature_test',
            'value' => 50,
            'currencyMode' => 'percent',
            'order' => 7,
        ];
        $this->sendPost(self::URI, $newFinanceCondition);

        $server = $this->transformHeadersToServerVars($this->headers($this->getUser()));
        $cookies = $this->prepareCookiesForRequest();
        $response = $this->call(
            'POST',
            self::URI . '/search',
            [],
            $cookies,
            [],
            $server,
            json_encode(['sorter' => ['fields' => [['field' => 'order', 'value' => 'desc'], ['field' => 'id', 'value' => 'asc']]]]),
        );

        $response->assertStatus(200);
        $response->assertJson(array (
            'data' =>
                array (
                    0 =>
                        array (
                            'id' => 3,
                            'title' => 'feature_test',
                            'value' => '50',
                            'assistants' =>
                                array (
                                ),
                            'cities' =>
                                array (
                                ),
                            'doctors' =>
                                array (
                                ),
                            'services' =>
                                array (
                                ),
                            'datePeriods' =>
                                array (
                                ),
                            'type' => 'add',
                            'model' => 'undefined',
                            'currencyId' => 0,
                            'currencyMode' => 'percent',
                            'order' => '27',
                        ),
                    1 =>
                        array (
                            'id' => 2,
                            'title' => 'feature_test',
                            'value' => '50',
                            'assistants' =>
                                array (
                                ),
                            'cities' =>
                                array (
                                ),
                            'doctors' =>
                                array (
                                ),
                            'services' =>
                                array (
                                ),
                            'datePeriods' =>
                                array (
                                ),
                            'type' => 'add',
                            'model' => 'undefined',
                            'currencyId' => 0,
                            'currencyMode' => 'percent',
                            'order' => '17',
                        ),
                    2 =>
                        array (
                            'id' => 1,
                            'title' => 'feature_test',
                            'value' => '50',
                            'assistants' =>
                                array (
                                ),
                            'cities' =>
                                array (
                                ),
                            'doctors' =>
                                array (
                                ),
                            'services' =>
                                array (
                                ),
                            'datePeriods' =>
                                array (
                                ),
                            'type' => 'add',
                            'model' => 'undefined',
                            'currencyId' => 0,
                            'currencyMode' => 'percent',
                            'order' => '7',
                        ),
                    3 =>
                        array (
                            'id' => 4,
                            'title' => 'feature_test',
                            'value' => '50',
                            'assistants' =>
                                array (
                                ),
                            'cities' =>
                                array (
                                ),
                            'doctors' =>
                                array (
                                ),
                            'services' =>
                                array (
                                ),
                            'datePeriods' =>
                                array (
                                ),
                            'type' => 'add',
                            'model' => 'undefined',
                            'currencyId' => 0,
                            'currencyMode' => 'percent',
                            'order' => '7',
                        ),
                ),
            'meta' =>
                array (
                    'pagination' =>
                        array (
                            'total' => 4,
                            'count' => 4,
                            'per_page' => 25,
                            'current_page' => 1,
                            'total_pages' => 1,
                            'links' =>
                                array (
                                ),
                        ),
                ),
        ));
    }

    public function testStoreGlobalRule(): void
    {
        $newFinanceCondition = [
            'title' => 'feature_test',
            'value' => 50,
            'currencyMode' => 'percent',
            'order' => 7,
        ];
        $response = $this->sendPost(self::URI, $newFinanceCondition);
        $response->assertJson([
            'data' => [
                'title' => 'feature_test',
                'value' => 50,
                'currencyMode' => 'percent',
                'order' => 7,
            ],
        ]);
        $response->assertStatus(201);
    }

    public function testUpdateGlobalRule(): void
    {
        $condition = FinanceCondition::create([
            'title' => 'feature_test',
            'value' => 50,
            'currency_mode' => 'percent',
            'type' => 'add',
            'currency_id' => 0,
            'model' => Accident::class,
        ]);
        $response = $this->sendPut('/api/director/finance/' . $condition->id, [
            'value' => 51,
            'title' => 'feature_test',
            'currencyMode' => 'percent',
            'type' => 'add',
            'currencyId' => 0,
            'model' => 'assistant',
        ]);
        $response->assertJson(['data' => [
            'title' => 'feature_test',
            'value' => 51,
            'id' => $condition->id,
            'currencyMode' => 'percent',
            'type' => 'add',
            'currencyId' => 0,
            'model' => 'assistant',
            'cities' => [],
            'doctors' => [],
            'services' => [],
            'datePeriods' => [],
        ]]);
        $response->assertStatus(200);
    }

    public function testPreciseRule(): void
    {
        $newFinanceCondition = [
            'title' => 'precisionRuleUnitTest',
            'value' => 30,
            'currencyMode' => 'percent',
            'assistant' => AssistantFake::make()->toArray(),
            'city' => CityFake::make()->toArray(),
            'datePeriod' => DatePeriodFake::make()->toArray(),
            'doctor' => DoctorFake::make()->toArray(),
            'services' => [
                DoctorServiceFake::make()->toArray(),
                DoctorServiceFake::make()->toArray(),
                DoctorServiceFake::make()->toArray(),
                DoctorServiceFake::make()->toArray(),
            ],
        ];
        $response = $this->sendPost(self::URI, $newFinanceCondition);
        $response->assertJson(['data' => ['title' => 'precisionRuleUnitTest', 'value' => 30]]);
        $response->assertStatus(201);
    }

    public function testShow(): void
    {
        $financeCondition = $this->getServiceLocator()->get(FinanceCondition::class);
        $condition = $financeCondition->create([
            'title' => 'feature_test',
            'value' => 50,
            'currency_mode' => 'percent',
            'type' => 'add',
            'currency_id' => 0,
            'model' => Assistant::class,
        ]);
        $response = $this->sendGet('/api/director/finance/' . $condition->id);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => 1,
                'title' => 'feature_test',
                'value' => '50',
                'assistants' =>
                    array(),
                'cities' =>
                    array(),
                'doctors' =>
                    array(),
                'services' =>
                    array(),
                'datePeriods' =>
                    array(),
                'type' => 'add',
                'model' => 'assistant',
                'currencyId' => 0,
                'currencyMode' => 'percent',
            ],
        ]);
    }

    public function testDelete(): void
    {
        $financeCondition = $this->getServiceLocator()->get(FinanceCondition::class);
        $condition = $financeCondition->create([
            'title' => 'feature_test',
            'value' => 50,
            'currency_mode' => 'percent',
            'type' => 'add',
            'currency_id' => 0,
            'model' => Accident::class,
        ]);
        $response = $this->sendDelete('/api/director/finance/' . $condition->id);
        $response->assertStatus(204);
    }
}
