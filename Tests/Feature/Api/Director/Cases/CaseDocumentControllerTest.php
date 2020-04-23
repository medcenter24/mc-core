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

namespace medcenter24\mcCore\Tests\Feature\Api\Director\Cases;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use medcenter24\mcCore\App\Services\Entity\CaseAccidentService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class CaseDocumentControllerTest extends TestCase
{
    use DirectorTestTraitApi;

    /**
     * @var CaseAccidentService
     */
    private $caseAccidentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->caseAccidentService = new CaseAccidentService();
    }

    public function testDocuments(): void
    {
        Storage::fake('documents');

        $accident = $this->caseAccidentService->create();
        $response = $this->sendGet('/api/director/cases/' . $accident->id . '/documents');
        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [],
        ]);

        $addResponse = $this->sendPost('/api/director/cases/' . $accident->id . '/documents',
            [
                'image' => UploadedFile::fake()->image('fake.jpg'),
                'image2' => UploadedFile::fake()->image('fake2.jpg')
            ]);

        $addResponse->assertStatus(200);
        $addResponse->assertExactJson([
            'data' => [
                [
                    'b64thumb' => '',
                    'id' => 1,
                    'fileName' => null,
                    'owner' => 'accident',
                    'title' => 'fake.jpg',
                    'type' => '',
                ],
                [
                    'b64thumb' => '',
                    'id' => 2,
                    'fileName' => null,
                    'owner' => 'accident',
                    'title' => 'fake2.jpg',
                    'type' => '',
                ],
            ]
        ]);

        $response = $this->get('/api/director/cases/' . $accident->id . '/documents', $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson([
            'data' =>
                [
                    [
                        'id' => 1,
                        'title' => 'fake.jpg',
                        'type' => 'passport',
                        'owner' => 'accident',
                        'fileName' => NULL,
                        'b64thumb' => '',
                    ],
                    [
                        'id' => 2,
                        'title' => 'fake2.jpg',
                        'type' => 'passport',
                        'owner' => 'accident',
                        'fileName' => NULL,
                        'b64thumb' => '',
                    ],
                ],
        ]);
    }
}
