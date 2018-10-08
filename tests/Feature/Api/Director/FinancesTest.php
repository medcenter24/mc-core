<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director;


use App\Assistant;
use App\City;
use App\DatePeriod;
use App\Doctor;
use App\DoctorService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;
use Tests\TestCase;

class FinancesTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    /**
     * @var array
     */
    protected $financeData;

    protected function setUp()
    {
        parent::setUp();

        $assistants = factory(Assistant::class, 2)->create();
        $cities = factory(City::class, 2)->create();
        $doctors = factory(Doctor::class, 2)->create();
        $services = factory(DoctorService::class, 2)->create();
        $datePeriods = factory(DatePeriod::class, 2)->create();

        $this->financeData = [
            'title' => 'Unit test rule',
            'value' => 11,
            'currencyMode' => 'currency',
            'currencyId' => 0,
            'model' => Doctor::class,
            'type' => 'sub',
            'assistants' => $assistants->map(function($v) { return $v['id']; }),
            'cities' => $cities->map(function($v) { return $v['id']; }),
            'doctors' => $doctors->map(function($v) { return $v['id']; }),
            'services' => $services->map(function($v) { return $v['id']; }),
            'datePeriods' => $datePeriods->map(function($v) { return $v['id']; }),
        ];
    }

    public function testStoreRule()
    {
        $response = $this->json('POST', '/api/director/finance', $this->financeData, $this->headers($this->getUser()));
        $response->assertJson([
            'title' => 'Unit test rule',
            'value' => 11,
        ]);
        $response->assertStatus(201);
    }

    public function testUpdateRule()
    {
        $response = $this->json('POST', '/api/director/finance', $this->financeData, $this->headers($this->getUser()));
        $response->assertStatus(201);
        $data = $response->json();
        $data['title'] = 'newTitle';
        $data['value'] = '9';
        $updateResponse = $this->json('PUT', "/api/director/finance/{$response->json('id')}", $data, $this->headers($this->getUser()));
        $updateResponse->assertJson(['data' => ['title' => 'newTitle', 'value' => 9]]);
        $updateResponse->assertStatus(200);
    }

    public function testGetRule()
    {
        $response = $this->json('POST', '/api/director/finance', $this->financeData, $this->headers($this->getUser()));
        $response->assertStatus(201);
        $getResponse = $this->json('GET', "/api/director/finance/{$response->json('id')}", [], $this->headers($this->getUser()));
        $getResponse->assertJson(['data' => []]);
        $getResponse->assertStatus(200);
    }
}
