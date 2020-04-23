<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\Tests\Feature\Api\Doctor\Accident;

use Illuminate\Http\UploadedFile;
use medcenter24\mcCore\App\Entity\Document;
use medcenter24\mcCore\Tests\TestCase;

class DocumentsAccidentControllerTest extends TestCase
{
    use TestDoctorAccidentTrait;

    public function testDocuments(): void
    {
        $case = $this->createAccidentForDoc();
        $docs = factory(Document::class, 5)->create();
        $case->documents()->attach($docs);
        self::assertEquals(5, $case->documents()->count());

        $response = $this->sendGet('/api/doctor/accidents/' . $case->id . '/documents');
        $response->assertStatus(200);
        $response->assertJson(['data' => [[], [], [], [], []]]);
    }

    public function testCreateDocument(): void
    {
        $case = $this->createAccidentForDoc();

        // first file will be used only (loading by file)
        $response = $this->sendPost('/api/doctor/accidents/' . $case->id . '/documents', [
            UploadedFile::fake()->image('fake.jpg'),
            UploadedFile::fake()->image('fake.jpg'),
            UploadedFile::fake()->image('fake.jpg'),
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => 1,
                'title' => 'fake.jpg',
                'type' => '',
                'owner' => 'doctor',
                'fileName' => NULL,
            ]
        ]);
    }
}
