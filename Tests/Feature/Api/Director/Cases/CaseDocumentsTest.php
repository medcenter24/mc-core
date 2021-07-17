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

namespace medcenter24\mcCore\Tests\Feature\Api\Director\Cases;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Document;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class CaseDocumentsTest extends TestCase
{
    use DirectorTestTraitApi;

    public function testGetNoDocuments(): void
    {
        $case = Accident::factory()->create();
        $response = $this->sendGet('/api/director/cases/' . $case->id .'/documents');
        $response->assertStatus(200);
        $response->assertJson(['data' => []]);
    }

    public function testGetDocuments(): void
    {
        $case = Accident::factory()->create();
        $docs = Document::factory()->count(5)->create();
        $case->documents()->attach($docs);
        self::assertEquals(5, $case->documents()->count());

        $response = $this->sendGet('/api/director/cases/' . $case->id .'/documents');
        $response->assertStatus(200);
        $response->assertJson(['data' => [[], [], [], [], []]]);
    }

    public function testGetWithoutUsersDocuments(): void
    {
        $case = Accident::factory()->create();
        $docs = Document::factory()->count(5)->create();
        $case->documents()->attach($docs);
        self::assertEquals(5, $case->documents()->count());
        $user = $this->getUser();
        $docs = Document::factory()->count(2)->create();
        $user->documents()->attach($docs);
        self::assertEquals(2, $user->documents()->count());

        $response = $this->sendGet('/api/director/cases/' . $case->id .'/documents');

        $response->assertStatus(200);
        $response->assertJson(['data' => [[], [], [], [], []]]);
    }
}
