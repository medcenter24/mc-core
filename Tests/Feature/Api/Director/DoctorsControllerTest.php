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

declare(strict_types=1);

namespace medcenter24\mcCore\Tests\Feature\Api\Director;

use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Services\Entity\CityService;
use medcenter24\mcCore\App\Services\Entity\DoctorService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorApiModelTest;

class DoctorsControllerTest extends DirectorApiModelTest
{

    private const URI = 'api/director/doctors';

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
        return DoctorService::class;
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
                    'message' => '422 Unprocessable Content',
                    'errors' => [
                        'name' => ['The name field is required.'],
                        'refKey' => ['The ref key field is required.']
                    ],
                    'status_code' => 422,
                ],
            ],
            [
                'data' => ['name' => ''],
                'expectedResponse' => [
                    'message' => '422 Unprocessable Content',
                    'errors' => [
                        'name' => ['The name field is required.'],
                        'refKey' => ['The ref key field is required.']
                    ],
                    'status_code' => 422,
                ],
            ],
            [
                'data' => ['name' => '123'],
                'expectedResponse' => [
                    'message' => '422 Unprocessable Content',
                    'errors' =>
                        [
                            'refKey' =>
                                [
                                    0 => 'The ref key field is required.',
                                ],
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
                    'name' => '123',
                    'refKey' => 'ref',
                ],
                'expectedResponse' => [
                    'data' => [
                        'id' => 1,
                        'name' => '123',
                        'description' => '',
                        'refKey' => 'ref',
                        'userId' => '0',
                        'medicalBoardNumber' => '',
                    ],
                ],
            ],
            [
                'data' => [
                    'name' => 'Php Unit test',
                    'refKey' => 'ref',
                    'description' => '1234',
                    'userId' => 2,
                    'medicalBoardNumber' => 'aaa',
                ],
                'expectedResponse' => [
                    'data' => [
                        'id' => 1,
                        'name' => 'Php Unit test',
                        'description' => '1234',
                        'refKey' => 'ref',
                        'userId' => 2,
                        'medicalBoardNumber' => 'aaa',
                    ]
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
                    'name' => '123',
                    'ref_key' => 'ref0',
                ],
                'updateData' => [
                    'id' => 1,
                    'name' => 'Php Unit test',
                    'refKey' => 'ref1',
                    'description' => '1234',
                    'userId' => 2,
                    'medicalBoardNumber' => 'aaa',
                ],
                'expectedResponse' => [
                    'data' => [
                        'id' => 1,
                        'name' => 'Php Unit test',
                        'refKey' => 'ref1',
                        'description' => '1234',
                        'userId' => 2,
                        'medicalBoardNumber' => 'aaa',
                    ]
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
                    ['name' => 'Text to be searched'],
                    ['name' => 'Php Unit test'],
                    ['name' => 'another text'],
                ],
                // filters
                [
                    'filters' => [],
                ],
                // response
                'expectedResponse' => [
                    'data' => [
                        [
                            'id' => 1,
                            'name' => 'Text to be searched',
                        ],
                        [
                            'id' => 2,
                            'name' => 'Php Unit test',
                        ],
                        [
                            'id' => 3,
                            'name' => 'another text',
                        ],
                    ],
                    'meta' => [
                        'pagination' => [
                            'total' => 3,
                            'count' => 3,
                            'per_page' => 25,
                            'current_page' => 1,
                            'total_pages' => 1,
                            'links' => [
                            ],
                        ],
                    ],

                ],
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function showDataProvider(): array
    {
        return [
            [
                'data' => ['name' => '123'],
                'expectedResponse' => [
                    'data' => [
                        'id' => 1,
                        'name' => '123',
                    ]
                ],
            ],
            [
                'data' => [
                    'name' => 'Php Unit test',
                ],
                'expectedResponse' => [
                    'data' => [
                        'id' => 1,
                        'name' => 'Php Unit test',
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function deleteDataProvider(): array
    {
        return [
            [
                'data' => ['name' => '123'],
            ],
            [
                'data' => [
                    'name' => 'Php Unit test',
                ],
            ],
        ];
    }

    public function testGetCities(): void
    {
        /** @var Doctor $doctor */
        $doctor = $this->getServiceLocator()->get(DoctorService::class)->create();
        $response = $this->json('GET', $this->getUri() . '/' . $doctor->id . '/cities', [], $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [],
        ]);
    }

    public function testSetCities(): void
    {
        $cityService = $this->getServiceLocator()->get(CityService::class);

        /** @var Doctor $doctor */
        $doctor = $this->getServiceLocator()->get(DoctorService::class)->create();
        $response = $this->json('PUT', $this->getUri() . '/' . $doctor->id . '/cities', [
            'cities' => [
                $cityService->create()->id,
                $cityService->create()->id,
                $cityService->create()->id,
                $cityService->create()->id,
            ]
        ], $this->headers($this->getUser()));
        $response->assertStatus(202);
        $doctor->refresh();
        $this->assertCount(4, $doctor->cities);
    }

    public function testDoctorsByCity(): void
    {
        $cityService = $this->getServiceLocator()->get(CityService::class);
        $id = $cityService->create()->id;
        $response = $this->json('GET', $this->getUri() . '/cities/' . $id, [], $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [],
        ]);
    }
}
