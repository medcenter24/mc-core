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

namespace medcenter24\mcCore\Tests\Feature\Api\Director\Cases;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class DirectorCaseServicesTest extends TestCase
{
    use DirectorTestTraitApi;

    public function testGetNoServices(): void
    {
        $case = Accident::factory()->create();
        $response = $this->sendGet('/api/director/cases/' . $case->id .'/services');
        $response->assertStatus(200);
        $response->assertJson(['data' => []]);
    }

    public function testGetServices(): void
    {
        $caseable = DoctorAccident::factory()->create();

        $accident = Accident::factory()->create();
        $accident->caseable_id = $caseable->id;
        $accident->caseable_type = DoctorAccident::class;
        $accident->save();

        $services = Service::factory()->count(5)->create();
        $accident->caseable->services()->attach($services);
        self::assertEquals(5, $accident->caseable->services->count());

        $response = $this->sendGet('/api/director/cases/' . $accident->id .'/services');
        $response->assertStatus(200);
        $response->assertJson(['data' => [[], [], [], [], []]]);
    }
}
