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

namespace medcenter24\mcCore\Tests\Feature\Api\Director;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use medcenter24\mcCore\App\Entity\Document;
use medcenter24\mcCore\App\Services\Entity\DocumentService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\Helper\FakeImage;
use medcenter24\mcCore\Tests\TestCase;
use Spatie\MediaLibrary\MediaCollections\Exceptions\DiskDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class DocumentsControllerTest extends TestCase
{
    use DirectorTestTraitApi;

    public function getDocumentService(): DocumentService
    {
        return $this->getServiceLocator()->get(DocumentService::class);
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function testShow(): void
    {
        Storage::fake('documents');

        $doc = $this->getDocumentService()
            ->createDocumentFromFile(
                FakeImage::getImage('fake.jpg'),
                $this->getUser()
            );

        $response = $this->sendGet('/api/director/documents/' . $doc->id);
        $response->assertOk();
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function testDelete(): void
    {
        Storage::fake('documents');

        $doc = $this->getDocumentService()
            ->createDocumentFromFile(
                FakeImage::getImage('fake.jpg'),
                $this->getUser()
            );

        $response = $this->sendDelete('/api/director/documents/' . $doc->id);
        $response->assertStatus(204);
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function testUpdate(): void
    {
        Storage::fake('documents');

        $doc = $this->getDocumentService()
            ->createDocumentFromFile(
                FakeImage::getImage('fake.jpg'),
                $this->getUser()
            );

        $response = $this->sendPut('/api/director/documents/' . $doc->id, [
            'type' => DocumentService::TYPE_INSURANCE,
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => 1,
                'title' => 'fake.jpg',
                'type' => 'insurance',
                'owner' => 'user',
                'fileName' => NULL,
            ],
        ]);
    }
}
