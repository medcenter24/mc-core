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

class CasesControllerCloseAndDeleteActionTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testDeleteCase(){
        factory(Accident::class)->create();
        $response = $this->delete('/api/director/cases/1', [], $this->headers($this->getUser()));

        $response->assertStatus(204);
    }

    public function testCloseCase(){
        factory(Accident::class)->create();
        $response = $this->put('/api/director/cases/1/close', [], $this->headers($this->getUser()));

        $response->assertStatus(204);
    }
}
