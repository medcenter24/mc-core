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
use medcenter24\mcCore\App\Entity\Upload;
use medcenter24\mcCore\App\Entity\User;
use medcenter24\mcCore\App\Services\UploaderService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class UploadsControllerTest extends TestCase
{
    use DirectorTestTraitApi;

    private const URI = '/api/director/uploads';

    public function testStore(): void
    {
        Storage::fake('uploads');

        $response = $this->sendPost(self::URI, [
            UploadedFile::fake()->create('f1.txt'),
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'id' => 1,
            'name' => 'f1.txt',
        ]);
    }

    public function testShow(): void
    {
        $this->sendPost(self::URI, [
            UploadedFile::fake()->create('f1.txt'),
        ]);

        $response = $this->sendGet(self::URI . '/1');
        $response->assertStatus(200);
    }
}
