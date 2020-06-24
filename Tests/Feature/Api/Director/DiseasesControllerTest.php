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

use medcenter24\mcCore\App\Services\Entity\DiseaseService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorApiModelTest;

class DiseasesControllerTest extends DirectorApiModelTest
{

    private const URI = 'api/director/diseases';

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
        return DiseaseService::class;
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
                        'title' => ['The title field is required.']
                    ],
                ],
            ],
            [
                'data' => ['title' => ''],
                'expectedResponse' => [
                    'message' => '422 Unprocessable Entity',
                    'errors' =>
                        [
                            'title' =>
                                [
                                    0 => 'The title field is required.',
                                ],
                        ],
                    'status_code' => 422,
                ],
            ],
            [
                'data' => ['title' => '1234'],
                'expectedResponse' => [
                    'message' => '422 Unprocessable Entity',
                    'errors' =>
                        [
                            'code' =>
                                [
                                    'The code field is required.',
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
                    'title' => '123',
                    'code' => 'a',
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'title' => '123',
                ],
            ],
            [
                'data' => [
                    'title' => 'Php Unit test',
                    'code' => 'a',
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'title' => 'Php Unit test',
                    'code' => 'a',
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
                    'title' => '123',
                    'code' => 'a',
                ],
                'updateData' => [
                    'id' => 1,
                    'title' => 'Php Unit test',
                    'code' => 'b',
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'title' => 'Php Unit test',
                    'code' => 'b',
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
                        'title' => 'Text to be searched',
                        'code' => 'a',
                    ],
                    [
                        'title' => 'Php Unit test',
                        'code' => 'a',
                    ],
                    [
                        'title' => 'another text',
                        'code' => 'a',
                    ],
                ],
                // filters
                [
                    'filters' => ['code' => 'a',],
                ],
                // response
                'expectedResponse' => [
                    'data' => [
                        [
                            'id' => 1,
                            'title' => 'Text to be searched',
                            'code' => 'a',
                        ],
                        [
                            'id' => 2,
                            'title' => 'Php Unit test',
                            'code' => 'a',
                        ],
                        [
                            'id' => 3,
                            'title' => 'another text',
                            'description' => '',
                            'code' => 'a',
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
                'data' => ['title' => '123'],
                'expectedResponse' => [
                    'data' => [
                        'id' => 1,
                        'title' => '123',
                    ]
                ],
            ],
            [
                'data' => [
                    'title' => 'Php Unit test',
                ],
                'expectedResponse' => [
                    'data' => [
                        'id' => 1,
                        'title' => 'Php Unit test',
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
