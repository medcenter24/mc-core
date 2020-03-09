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

namespace medcenter24\mcCore\Tests\Feature\Api\Director\Cases\CaseController;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusesService;
use medcenter24\mcCore\Tests\Feature\Api\JwtHeaders;
use medcenter24\mcCore\Tests\Feature\Api\LoggedUser;
use medcenter24\mcCore\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CasesControllerSearchActionTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testSearch(): void
    {
        factory(Accident::class, 7)->create();
        $response = $this->post('/api/director/cases/search', [], $this->headers($this->getUser()));

        $response->assertStatus(200)->assertJson([
            'data' => [
                ['id' => 1]
            ],
            'meta' => [
                'pagination' => [
                    'total' => 7,
                    'count' => 7,
                    'per_page' => 15,
                    'current_page' => 1,
                    'total_pages' => 1,
                    'links' => [],
                ]
            ]
        ]);
    }

    public function testSearchClosed(): void
    {
        // I can't create closed Accident at all
        (new AccidentService())->create([
            'accident_status_id' => (new AccidentStatusesService())->getClosedStatus()->getAttribute('id'),
        ]);
        $response = $this->post('/api/director/cases/search', [], $this->headers($this->getUser()));

        $response->assertStatus(200)->assertJson([
            'data' => [
                ['id' => 1]
            ],
            'meta' => [
                'pagination' => [
                    'total' => 1,
                    'count' => 1,
                    'per_page' => 15,
                    'current_page' => 1,
                    'total_pages' => 1,
                    'links' => [],
                ]
            ]
        ]);
    }
}
