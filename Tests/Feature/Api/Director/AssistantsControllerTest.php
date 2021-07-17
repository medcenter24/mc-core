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

use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Filter;
use medcenter24\mcCore\App\Services\Entity\AssistantService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorApiModelTest;

class AssistantsControllerTest extends DirectorApiModelTest
{
    private const URI = 'api/director/assistants';

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
        return AssistantService::class;
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
                    'refKey' => 'ref',
                    'email' => 'unit@test.php',
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'title' => '123',
                    'refKey' => 'ref',
                    'email' => 'unit@test.php',
                ],
            ],
            [
                'data' => [
                    'title' => 'Php Unit test',
                    'refKey' => 'ref',
                    'email' => 'unit@test.php',
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'title' => 'Php Unit test',
                    'refKey' => 'ref',
                    'email' => 'unit@test.php',
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
                    'refKey' => 'ref',
                    'email' => 'unit@test.php',
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'title' => 'Php Unit test',
                    'refKey' => 'ref',
                    'email' => 'unit@test.php',
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
                            'email' => '',
                            'comment' => '',
                            'refKey' => '',
                        ],
                        [
                            'id' => 2,
                            'title' => 'Php Unit test',
                            'email' => '',
                            'comment' => '',
                            'refKey' => '',
                        ],
                        [
                            'id' => 3,
                            'title' => 'another text',
                            'email' => '',
                            'comment' => '',
                            'refKey' => '',
                        ]
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

    public function testFilteredSearch(): void
    {
        Assistant::factory()->create(['title' => 'Text to be searched']);
        Assistant::factory()->create(['title' => 'Php Unit test']);
        Assistant::factory()->create(['title' => 'another text']);

        $response = $this->sendPost(self::URI . '/search', [
            'filter' => [
                'fields' => [
                    [
                        Filter::FIELD_NAME => 'title',
                        Filter::FIELD_MATCH => Filter::MATCH_CONTENTS,
                        Filter::FIELD_VALUE => 'php',
                        Filter::FIELD_EL_TYPE => Filter::TYPE_TEXT,
                    ],
                ],
            ],
        ]);

        $response->assertOk()->assertJson([
            'data' => [
                [
                    'id' => 2,
                    'title' => 'Php Unit test',
                ],
            ],
            'meta' => [
                'pagination' => [
                    'total' => 1,
                    'count' => 1,
                    'per_page' => 25,
                    'current_page' => 1,
                    'total_pages' => 1,
                    'links' => [
                    ],
                ],
            ],
        ]);
    }
}
