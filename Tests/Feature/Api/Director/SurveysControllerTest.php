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
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\Tests\Feature\Api\Director;

use medcenter24\mcCore\App\Services\Entity\SurveyService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorApiModelTest;

class SurveysControllerTest extends DirectorApiModelTest
{
    private const URI = 'api/director/surveys';

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
        return SurveyService::class;
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
                        'title' => ['The title field is required.']
                    ],
                    'status_code' => 422,
                ],
            ],
            [
                'data' => [SurveyService::FIELD_TITLE => ''],
                'expectedResponse' => [
                    'message' => '422 Unprocessable Content',
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
                'data' => [SurveyService::FIELD_TITLE => '1'],
                'expectedResponse' => [
                    'message' => '422 Unprocessable Content',
                    'errors' =>
                        [
                            'title' =>
                                [
                                    0 => 'The title must be at least 3 characters.',
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
                'data' => ['title' => '123'],
                'expectedResponse' => [
                    'id' => 1,
                    'title' => '123',
                    'description' => '',
                    'type' => 'director',
                    'status' => 'active',
                ],
            ],
            [
                'data' => [
                    'title' => 'Php Unit test',
                    'description' => 'Desc',
                    'status' => 'disabled'
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'title' => 'Php Unit test',
                    'description' => 'Desc',
                    'type' => 'director',
                    'status' => 'disabled',
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
                'data' => ['title' => '123'],
                'updateData' => [
                    'id' => 1,
                    'title' => 'Php Unit test',
                    'description' => 'Desc',
                    'type' => 'doc',
                    'status' => 'disabled'
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'title' => 'Php Unit test',
                    'description' => 'Desc',
                    'type' => 'system',
                    'status' => 'disabled',
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
                            'description' => '',
                            'status' => 'active',
                            'type' => 'system',
                        ],
                        [
                            'id' => 2,
                            'title' => 'Php Unit test',
                            'description' => '',
                            'status' => 'active',
                            'type' => 'system',
                        ],
                        [
                            'id' => 3,
                            'title' => 'another text',
                            'description' => '',
                            'status' => 'active',
                            'type' => 'system',
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
                'data' => [SurveyService::FIELD_TITLE => '123'],
                'expectedResponse' => [
                    'data' => [
                        'id' => 1,
                        'title' => '123',
                        'description' => '',
                        'type' => 'system',
                        'status' => 'active',
                    ]
                ],
            ],
            [
                'data' => [
                    SurveyService::FIELD_TITLE => 'Php Unit test',
                    SurveyService::FIELD_DESCRIPTION => 'Desc',
                    SurveyService::FIELD_STATUS => 'disabled'
                ],
                'expectedResponse' => [
                    'data' => [
                        'id' => 1,
                        'title' => 'Php Unit test',
                        'description' => 'Desc',
                        'type' => 'system',
                        'status' => 'disabled',
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
                    'description' => 'Desc',
                    'status' => 'disabled'
                ],
            ],
        ];
    }
}
