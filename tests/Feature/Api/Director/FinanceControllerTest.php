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

namespace medcenter24\mcCore\Tests\Feature\Api\Director;


use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\FinanceCondition;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use medcenter24\mcCore\Tests\Feature\Api\JwtHeaders;
use medcenter24\mcCore\Tests\Feature\Api\LoggedUser;
use medcenter24\mcCore\Tests\TestCase;
use medcenter24\mcCore\Tests\Unit\fakes\DoctorServiceFake;
use medcenter24\mcCore\Tests\Unit\fakes\AssistantFake;
use medcenter24\mcCore\Tests\Unit\fakes\CityFake;
use medcenter24\mcCore\Tests\Unit\fakes\DatePeriodFake;
use medcenter24\mcCore\Tests\Unit\fakes\DoctorFake;

class FinanceControllerTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testStoreError(): void
    {
        $newFinanceCondition = [];
        $this->doNotPrintErrResponse(true);
        $response = $this->json('POST', '/api/director/finance', $newFinanceCondition, $this->headers($this->getUser()));
        $this->doNotPrintErrResponse(false);
        $response->assertStatus(422);
        self::assertArrayHasKey('title', $response->json('errors'), 'Error with `title` message exists');
        self::assertArrayHasKey('value', $response->json('errors'), 'Error with `value` message exists');
        self::assertArrayHasKey('currencyMode', $response->json('errors'), 'Error with `currencyMode` message exists');
    }

    public function testStoreGlobalRule(): void
    {
        $newFinanceCondition = [
            'title' => 'feature_test',
            'value' => 50,
            'currencyMode' => 'percent'
        ];
        $response = $this->json('POST', '/api/director/finance', $newFinanceCondition, $this->headers($this->getUser()));
        $response->assertJson([
            'title' => 'feature_test',
            'value' => 50,
            'currencyMode' => 'percent'
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
        $response = $this->json('PUT', '/api/director/finance/' . $condition->id, [
            'value' => 51,
            'title' => 'feature_test',
            'currencyMode' => 'percent',
            'type' => 'add',
            'currencyId' => 0,
            'model' => Accident::class,
        ], $this->headers($this->getUser()));
        $response->assertJson(['data' => [
            'title' => 'feature_test',
            'value' => 51,
            'id' => $condition->id,
            'currencyMode' => 'percent',
            'type' => 'add',
            'currencyId' => 0,
            'model' => Accident::class,
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
        $response = $this->json('POST', '/api/director/finance', $newFinanceCondition, $this->headers($this->getUser()));
        $response->assertJson(['title' => 'precisionRuleUnitTest', 'value' => 30]);
        $response->assertStatus(201);
    }
}
