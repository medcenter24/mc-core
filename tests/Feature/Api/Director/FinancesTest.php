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

namespace medcenter24\mcCore\Tests\Feature\Api\Director;

use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\City;
use medcenter24\mcCore\App\Entity\DatePeriod;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\Service;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use medcenter24\mcCore\Tests\Feature\Api\JwtHeaders;
use medcenter24\mcCore\Tests\Feature\Api\LoggedUser;
use medcenter24\mcCore\Tests\TestCase;

class FinancesTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    /**
     * @var array
     */
    protected $financeData;

    protected function setUp(): void
    {
        parent::setUp();

        $assistants = factory(Assistant::class, 2)->create();
        $cities = factory(City::class, 2)->create();
        $doctors = factory(Doctor::class, 2)->create();
        $services = factory(Service::class, 2)->create();
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

    public function testStoreRule(): void
    {
        $response = $this->json('POST', '/api/director/finance', $this->financeData, $this->headers($this->getUser()));
        $response->assertJson([
            'title' => 'Unit test rule',
            'value' => 11,
        ]);
        $response->assertStatus(201);
    }

    public function testUpdateRule(): void
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
