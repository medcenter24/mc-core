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

use Illuminate\Support\Facades\Storage;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Services\Entity\CaseAccidentService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\Helper\FakeImage;
use medcenter24\mcCore\Tests\TestCase;

class CaseDocumentControllerTest extends TestCase
{
    use DirectorTestTraitApi;

    private CaseAccidentService $caseAccidentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->caseAccidentService = new CaseAccidentService();
    }

    /**
     * @throws InconsistentDataException
     */
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
                'files' => [
                    'image' => FakeImage::getImage('fake.jpg'),
                    'image2' => FakeImage::getImage('fake2.jpg'),
                ]
            ]);

        $addResponse->assertStatus(200);
        $dataResponse = $addResponse->json('data');
        $this->assertCount(2, $dataResponse);

        $row1 = $dataResponse[0];
        $this->assertArrayHasKey('b64thumb', $row1);
        unset($row1['b64thumb']);
        $this->assertSame([
            'id' => 1,
            'title' => 'fake.jpg',
            'type' => 'passport',
            'owner' => 'accident',
            'fileName' => null,
        ], $row1);

        $row2 = $dataResponse[1];
        $this->assertArrayHasKey('b64thumb', $row2);
        unset($row2['b64thumb']);
        $this->assertSame([
            'id' => 2,
            'title' => 'fake2.jpg',
            'type' => 'passport',
            'owner' => 'accident',
            'fileName' => null,
        ], $row2);

        $response = $this->get('/api/director/cases/' . $accident->id . '/documents', $this->headers($this->getUser()));
        $response->assertStatus(200);
        $dataResponse = $addResponse->json('data');

        $row1 = $dataResponse[0];
        $this->assertArrayHasKey('b64thumb', $row1);
        unset($row1['b64thumb']);
        $this->assertSame([
            'id' => 1,
            'title' => 'fake.jpg',
            'type' => 'passport',
            'owner' => 'accident',
            'fileName' => NULL,
        ], $row1);

        $row2 = $dataResponse[1];
        $this->assertArrayHasKey('b64thumb', $row2);
        unset($row2['b64thumb']);
        $this->assertSame([
            'id' => 2,
            'title' => 'fake2.jpg',
            'type' => 'passport',
            'owner' => 'accident',
            'fileName' => NULL,
        ], $row2);
    }
}
