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

use medcenter24\mcCore\App\Services\Entity\PatientService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorApiModelTest;

class PatientsControllerTest extends DirectorApiModelTest
{

    private const URI = 'api/director/patients';

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
        return PatientService::class;
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
                        'name' => ['The name field is required.']
                    ],
                ],
            ],
            [
                'data' => ['name' => ''],
                'expectedResponse' => [
                    'message' => '422 Unprocessable Entity',
                    'errors' =>
                        [
                            'name' =>
                                [
                                    0 => 'The name field is required.',
                                ],
                        ],
                    'status_code' => 422,
                ],
            ],
            [
                'data' => ['name' => '', 'birthday' => 'bbb'],
                'expectedResponse' => [
                    'message' => '422 Unprocessable Entity',
                    'errors' =>
                        [
                            'birthday' =>
                                [
                                    0 => 'The birthday is not a valid date.',
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
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'name' => '123',
                ],
            ],
            [
                'data' => [
                    'name' => 'Php Unit test',
                    'address' => 'addr',
                    'phones' => 'phone',
                    'birthday' => '2020-01-02',
                    'comment' => 'bbb',
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'name' => 'Php Unit test',
                    'address' => 'addr',
                    'phones' => 'phone',
                    'birthday' => '2020-01-02',
                    'comment' => 'bbb',
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
                'data' => ['name' => '123'],
                'updateData' => [
                    'id' => 1,
                    'name' => 'Php Unit test',
                    'address' => 'addr',
                    'phones' => 'phone',
                    'birthday' => '2020-01-02',
                    'comment' => 'bbb',
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'name' => 'Php Unit test',
                    'address' => 'addr',
                    'phones' => 'phone',
                    'birthday' => '2020-01-02',
                    'comment' => 'bbb',
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
                    ['title' => 'Text to be searched'],
                    ['title' => 'Php Unit test'],
                    ['title' => 'another text'],
                ],
                // filters
                [
                    'filters' => [],
                ],
                // response
                'expectedResponse' => array (
                    'data' =>
                        array (
                            0 =>
                                array (
                                    'id' => 1,
                                    'name' => '',
                                    'address' => '',
                                    'phones' => '',
                                    'birthday' => '',
                                    'comment' => '',
                                ),
                            1 =>
                                array (
                                    'id' => 2,
                                    'name' => '',
                                    'address' => '',
                                    'phones' => '',
                                    'birthday' => '',
                                    'comment' => '',
                                ),
                            2 =>
                                array (
                                    'id' => 3,
                                    'name' => '',
                                    'address' => '',
                                    'phones' => '',
                                    'birthday' => '',
                                    'comment' => '',
                                ),
                        ),
                    'meta' =>
                        array (
                            'pagination' =>
                                array (
                                    'total' => 3,
                                    'count' => 3,
                                    'per_page' => 25,
                                    'current_page' => 1,
                                    'total_pages' => 1,
                                    'links' =>
                                        array (
                                        ),
                                ),
                        ),
                ),
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
                        'address' => '',
                        'phones' => '',
                        'birthday' => '',
                        'comment' => '',
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
                        'address' => '',
                        'phones' => '',
                        'birthday' => '',
                        'comment' => '',
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
                'data' => ['title' => '123'],
            ],
            [
                'data' => [
                    'title' => 'Php Unit test',
                ],
            ],
        ];
    }
}
