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

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Document;
use medcenter24\mcCore\Tests\Feature\Api\JwtHeaders;
use medcenter24\mcCore\Tests\Feature\Api\LoggedUser;
use medcenter24\mcCore\Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectorCaseDocumentsTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testGetNoDocuments(): void
    {
        $case = factory(Accident::class)->create();
        $response = $this->get('/api/director/cases/' . $case->id .'/documents', $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson(['data' => []]);
    }

    public function testGetDocuments(): void
    {
        $case = factory(Accident::class)->create();
        $docs = factory(Document::class, 5)->create();
        $case->documents()->attach($docs);
        self::assertEquals(5, $case->documents()->count());

        $response = $this->get('/api/director/cases/' . $case->id .'/documents', $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson(['data' => [[], [], [], [], []]]);
    }

    public function testGetWithoutUsersDocuments(): void
    {
        $case = factory(Accident::class)->create();
        $docs = factory(Document::class, 5)->create();
        $case->documents()->attach($docs);
        self::assertEquals(5, $case->documents()->count());
        $user = $this->getUser();
        $docs = factory(Document::class, 2)->create();
        $user->documents()->attach($docs);
        self::assertEquals(2, $user->documents()->count());

        $response = $this->get('/api/director/cases/' . $case->id .'/documents', $this->headers($this->getUser()));

        $response->assertStatus(200);
        $response->assertJson(['data' => [[], [], [], [], []]]);
    }


}
