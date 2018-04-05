<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director;


use App\Assistant;
use App\Doctor;
use App\FinanceCondition;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;
use Tests\TestCase;
use Tests\Unit\fakes\AccidentServiceFake;
use Tests\Unit\fakes\AssistantFake;
use Tests\Unit\fakes\CityFake;
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
        self::assertArrayHasKey('priceAmount', $response->json('errors'), 'Error with priceAmount message exists');
        $response->assertStatus(422);
    }

    public function testStoreGlobalRule()
    {
        $newFinanceCondition = [
            'title' => 'feature_test',
            'priceAmount' => 50,
        ];
        $response = $this->json('POST', '/api/director/finance', $newFinanceCondition, $this->headers($this->getUser()));
        $response->assertJson(['title' => 'feature_test', 'price' => 50]);
        $response->assertStatus(201);
    }

    public function testUpdateGlobalRule()
    {
        $condition = FinanceCondition::create(['title' => 'feature_test', 'price' => 50]);
        $response = $this->json('PUT', '/api/director/finance/' . $condition->id, ['priceAmount' => 51], $this->headers($this->getUser()));
        $response->assertJson(['data' => ['title' => 'feature_test', 'price' => 51, 'id' => $condition->id]]);
        $response->assertStatus(200);
    }

    public function testPreciseRule()
    {
        $newFinanceCondition = [
            'title' => 'precisionRuleUnitTest',
            'priceAmount' => 30,
            'assistant' => AssistantFake::make()->toArray(),
            'city' => CityFake::make()->toArray(),
            'datePeriod' => DatePeriodFake::make()->toArray(),
            'doctor' => DoctorFake::make()->toArray(),
            'services' => [
                AccidentServiceFake::make()->toArray(),
                AccidentServiceFake::make()->toArray(),
                AccidentServiceFake::make()->toArray(),
                AccidentServiceFake::make()->toArray(),
            ],
        ];
        $response = $this->json('POST', '/api/director/finance', $newFinanceCondition, $this->headers($this->getUser()));
        $response->assertJson(['title' => 'feature_test', 'price' => 50]);
        $response->assertStatus(201);
    }
}
