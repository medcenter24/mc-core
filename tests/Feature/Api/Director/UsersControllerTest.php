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

use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Services\Entity\DoctorService;
use medcenter24\mcCore\App\Services\Entity\RoleService;
use medcenter24\mcCore\App\Services\Entity\UserService;
use medcenter24\mcCore\App\Services\LogoService;
use medcenter24\mcCore\App\Entity\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use medcenter24\mcCore\Tests\Feature\Api\DirectorApiModelTest;

class UsersControllerTest extends DirectorApiModelTest
{
    public const URI = '/api/director/users';

    public function testOptions(): void
    {
        $user = factory(User::class)->create(['password' => bcrypt('foo')]);

        $response = $this->sendOptions(self::URI . '/' . $user->id . '/photo');
        $response->assertStatus(200)
            ->assertHeader('Allow', 'POST,DELETE');
    }

    public function testUpdatePhoto(): void
    {
        Storage::fake(LogoService::DISC);

        $user = factory(User::class)->create(['password' => bcrypt('foo')]);
        $response = $this->sendPost(self::URI . '/' . $user->id . '/photo',
            ['file' => UploadedFile::fake()->image('photo.jpg', 100, 100)]
        );
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => 1,
                'phone' => '',
                'lang' => '',
                'timezone' => 'UTC',
                'thumb200' => 'noContent',
                'thumb45' => 'noContent',
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function getUri(): string
    {
        return self::URI;
    }

    /**
     * @inheritDoc
     */
    protected function getModelServiceClass(): string
    {
        return UserService::class;
    }

    /**
     * @inheritDoc
     */
    public function failedDataProvider(): array
    {
        return [
            [
                'data' => [],
                'expectedResponse' => [
                    'message' => '422 Unprocessable Entity',
                    'errors' => [
                        'email' => ['The email field is required.'],
                        'password' => ['The password field is required.'],
                    ],
                ],
            ],
            [
                'data' => ['email' => '', 'password' => ''],
                'expectedResponse' => [
                    'message' => '422 Unprocessable Entity',
                    'errors' =>
                        [
                            'email' => ['The email field is required.'],
                            'password' => ['The password field is required.'],
                        ],
                    'status_code' => 422,
                ],
            ],
            [
                'data' => ['email' => 'a', 'password' => 'a'],
                'expectedResponse' => [
                    'message' => '422 Unprocessable Entity',
                    'errors' =>
                        [
                            'email' => ['The email must be a valid email address.'],
                        ],
                    'status_code' => 422,
                ],
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function createDataProvider(): array
    {
        return [
            [
                'data' => [
                    'email' => '123@mc24.test',
                    'password' => 'a',
                ],
                'expectedResponse' => [
                    'id' => 2,
                    'name' => '',
                    'email' => '123@mc24.test',
                    'phone' => '',
                    'lang' => 'en',
                    'timezone' => 'UTC',
                    'thumb200' => '',
                    'thumb45' => '',
                ],
            ],
            [
                'data' => [
                    'email' => '123@mc24.test',
                    'password' => 'a',
                    'name' => 'User Name',
                    'phone' => '1234',
                    'lang' => 'es',
                    'timezone' => 'Europe/Spain',
                ],
                'expectedResponse' => [
                    'id' => 2,
                    'email' => '123@mc24.test',
                    'name' => 'User Name',
                    'phone' => '1234',
                    'lang' => 'es',
                    'timezone' => 'Europe/Spain',
                    'thumb200' => '',
                    'thumb45' => '',
                ],
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function updateDataProvider(): array
    {
        return [
            [
                'data' => [
                    'email' => '123@mc24.test',
                    'password' => 'a',
                ],
                'updateData' => [
                    'id' => 1,
                    'email' => '123@mc24.test',
                    'password' => 'a',
                    'name' => 'User Name',
                    'phone' => '1234',
                    'lang' => 'es',
                    'timezone' => 'Europe/Spain',
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'email' => '123@mc24.test',
                    'name' => 'User Name',
                    'phone' => '1234',
                    'lang' => 'es',
                    'timezone' => 'Europe/Spain',
                ],
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function searchDataProvider(): array
    {
        return [
            [
                'modelsData' => [
                    [
                        'email' => 'a@a.com',
                    ],
                ],
                // filters
                [
                    'filters' => [],
                ],
                // response
                // only doctors should be here
                'expectedResponse' => array (
                    'data' =>
                        array (
                            // no users with doctors assigned
                            // check only the controllers workflow
                        ),
                ),
            ],
        ];
    }

    /**
     * @dataProvider showDataProvider
     * @param array $data
     * @param array $expectedResponse
     */
    public function testShow(array $data, array $expectedResponse): void
    {
        $this->post($this->getUri(), [
            'email' => '123@mc24.test',
            'password' => 'a',
        ], $this->headers($this->getUser()));

        $response = $this->get($this->getUri().'/2', $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson(
            array (
                'data' =>
                    array (
                        'id' => 2,
                        'name' => '',
                        'email' => '123@mc24.test',
                        'phone' => '',
                        'lang' => 'en',
                        'timezone' => 'UTC',
                        'thumb200' => '',
                        'thumb45' => '',
                    ),
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function deleteDataProvider(): array
    {
        return [[[1]]];
    }

    /**
     * @dataProvider deleteDataProvider
     * @param array $data
     */
    public function testDelete(array $data): void
    {
        $user = $this->getUser([RoleService::DOCTOR_ROLE]);
        factory(Doctor::class)->create([
            DoctorService::FIELD_USER_ID => $user->getKey(),
        ]);
        $response = $this->sendDelete($this->getUri() . '/' . $user->getKey());
        $response->assertStatus(204);

        $response = $this->sendGet($this->getUri().'/'.$user->getKey());
        $response->assertStatus(404);
    }

    /**
     * @inheritDoc
     */
    public function showDataProvider(): array
    {
        return [[['a'],['b']]];
    }
}
