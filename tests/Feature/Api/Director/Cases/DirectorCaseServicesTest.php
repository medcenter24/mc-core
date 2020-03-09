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

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\Tests\Feature\Api\JwtHeaders;
use medcenter24\mcCore\Tests\Feature\Api\LoggedUser;
use medcenter24\mcCore\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DirectorCaseServicesTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testGetNoServices(): void
    {
        $case = factory(Accident::class)->create();
        $response = $this->get('/api/director/cases/' . $case->id .'/services', $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson(['data' => []]);
    }

    public function testGetServices(): void
    {
        $caseable = factory(DoctorAccident::class)->create();

        $accident = factory(Accident::class)->create();
        $accident->caseable_id = $caseable->id;
        $accident->caseable_type = DoctorAccident::class;
        $accident->save();

        $services = factory(Service::class, 5)->create();
        $accident->caseable->services()->attach($services);
        self::assertEquals(5, $accident->caseable->services->count());

        $response = $this->get('/api/director/cases/' . $accident->id .'/services', $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson(['data' => [[], [], [], [], []]]);
    }

}
