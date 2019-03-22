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

namespace Tests\Feature\Api\Director\Cases\CaseController;

use App\Accident;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CasesControllerSearchActionTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testSearch()
    {
        factory(Accident::class, 7)->create();
        $response = $this->post('/api/director/cases/search', [], $this->headers($this->getUser()));

        $response->assertStatus(200)->assertJson([
            'data' => [
                ['id' => 1]
            ],
            "meta" => [
                "pagination" => [
                    "total" => 7,
                    "count" => 7,
                    "per_page" => 3000,
                    "current_page" => 1,
                    "total_pages" => 1,
                    "links" => []
                ]
            ]
        ]);
    }
}
