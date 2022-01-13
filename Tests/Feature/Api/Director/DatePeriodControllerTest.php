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

use medcenter24\mcCore\App\Services\Entity\DatePeriodService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorApiModelTest;

class DatePeriodControllerTest extends DirectorApiModelTest
{

    private const URI = 'api/director/periods';

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
        return DatePeriodService::class;
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
                        'title'     => ['The title field is required.'],
                        'from'      => ['The from field is required.'],
                        'to'        => ['The to field is required.']
                    ],
                    'status_code' => 422,
                ],
            ],
            [
                'data' => ['title' => ''],
                'expectedResponse' => [
                    'message' => '422 Unprocessable Content',
                    'errors' =>
                        [
                            'title'     => ['The title field is required.'],
                            'from'      => ['The from field is required.'],
                            'to'        => ['The to field is required.']
                        ],
                    'status_code' => 422,
                ],
            ],
            [
                'data' => [
                    'title' => '123',
                    'from' => '1',
                    'to' => '2',
                ],
                'expectedResponse' => [
                    'message' => '422 Unprocessable Content',
                    'errors' =>
                        [
                            'from' =>
                                [
                                    'The from must be at least 3 characters.',
                                    'Incorrect period format',
                                ],
                            'to' =>
                                [
                                    'The to must be at least 3 characters.',
                                    'Incorrect period format',
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
                    'from' => '11:22',
                    'to' => '22:12',
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'title' => '123',
                    'from' => '11:22',
                    'to' => '22:12',
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
                    'from' => '11:22',
                    'to' => '22:12',
                ],
                'updateData' => [
                    'id' => 1,
                    'title' => 'Php Unit test',
                    'from' => '12:22',
                    'to' => '11:12',
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'title' => 'Php Unit test',
                    'from' => '12:22',
                    'to' => '11:12',
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
                'expectedResponse' => [
                    'data' => [
                        [
                            'id' => 1,
                            'title' => 'Text to be searched',
                        ],
                        [
                            'id' => 2,
                            'title' => 'Php Unit test',
                        ],
                        [
                            'id' => 3,
                            'title' => 'another text',
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
