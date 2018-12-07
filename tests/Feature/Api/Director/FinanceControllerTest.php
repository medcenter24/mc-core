<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director;


use App\Accident;
use App\FinanceCondition;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;
use Tests\TestCase;
use Tests\Unit\fakes\DoctorServiceFake;
use Tests\Unit\fakes\AssistantFake;
use Tests\Unit\fakes\CityFake;
use Tests\Unit\fakes\DatePeriodFake;
use Tests\Unit\fakes\DoctorFake;

class FinanceControllerTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testStoreError()
    {
        $newFinanceCondition = [];
        $response = $this->json('POST', '/api/director/finance', $newFinanceCondition, $this->headers($this->getUser()));

        self::assertArrayHasKey('title', $response->json('errors'), 'Error with `title` message exists');
        self::assertArrayHasKey('value', $response->json('errors'), 'Error with `value` message exists');
        self::assertArrayHasKey('currencyMode', $response->json('errors'), 'Error with `currencyMode` message exists');
        $response->assertStatus(422);
    }

    public function testStoreGlobalRule()
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

    public function testUpdateGlobalRule()
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

    public function testPreciseRule()
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
