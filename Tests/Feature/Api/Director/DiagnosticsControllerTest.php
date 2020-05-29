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

use medcenter24\mcCore\App\Entity\Diagnostic;
use medcenter24\mcCore\App\Entity\DiagnosticCategory;
use medcenter24\mcCore\App\Entity\Disease;
use medcenter24\mcCore\App\Services\Entity\DiagnosticService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorApiModelTest;

class DiagnosticsControllerTest extends DirectorApiModelTest
{

    private const URI = 'api/director/diagnostics';

    /**
     * @inheritDoc
     */
    protected function getUri(): string
    {
        return self::URI;
    }

    protected function getModelServiceClass(): string
    {
        return DiagnosticService::class;
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
                'data' => [DiagnosticService::FIELD_TITLE => ''],
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
                'data' => [DiagnosticService::FIELD_TITLE => '1'],
                'expectedResponse' => [
                    'message' => '422 Unprocessable Entity',
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

    public function createDataProvider(): array
    {
        return [
            [
                'data' => ['title' => '123'],
                'expectedResponse' => [
                    'id' => 1,
                    'title' => '123',
                    'description' => '',
                    'diagnosticCategoryId' => 0,
                    'diseases' => [],
                    'type' => 'director',
                    'status' => 'active',
                ],
            ],
            [
                'data' => [
                    'title' => 'Php Unit test diagnostic',
                    'description' => 'Desc',
                    'diagnosticCategoryId' => 1,
                    'diseases' => [],
                    'status' => 'disabled'
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'title' => 'Php Unit test diagnostic',
                    'description' => 'Desc',
                    'diagnosticCategoryId' => 1,
                    'diseases' => [],
                    'type' => 'director',
                    'status' => 'disabled',
                ],
            ],
        ];
    }

    public function showDataProvider(): array
    {
        return [
            [
                'data' => [DiagnosticService::FIELD_TITLE => '123'],
                'expectedResponse' => [
                    'data' => [
                        'id' => 1,
                        'title' => '123',
                        'description' => '',
                        'diagnosticCategoryId' => 0,
                        'type' => 'system',
                        'status' => 'active',
                    ]
                ],
            ],
            [
                'data' => [
                    DiagnosticService::FIELD_TITLE => 'Php Unit test diagnostic',
                    DiagnosticService::FIELD_DESCRIPTION => 'Desc',
                    DiagnosticService::FIELD_DIAGNOSTIC_CATEGORY_ID => 1,
                    DiagnosticService::FIELD_STATUS => 'disabled'
                ],
                'expectedResponse' => [
                    'data' => [
                        'id' => 1,
                        'title' => 'Php Unit test diagnostic',
                        'description' => 'Desc',
                        'diagnosticCategoryId' => 1,
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
    public function updateDataProvider(): array
    {
        return [
            [
                'data' => ['title' => '123'],
                'updateData' => [
                    'id' => 1,
                    'title' => 'Php Unit test diagnostic',
                    'description' => 'Desc',
                    'diagnosticCategoryId' => 1,
                    'type' => 'doc',
                    'status' => 'disabled',
                    'diseases' => [],

                ],
                'expectedResponse' => [
                    'id' => 1,
                    'title' => 'Php Unit test diagnostic',
                    'description' => 'Desc',
                    'diagnosticCategoryId' => 1,
                    'diseases' => [],
                    'type' => 'system',
                    'status' => 'disabled',
                ],
            ],
        ];
    }

    /**
     * array $modelsData, array $filters, array $expectedResponse
     */
    public function searchDataProvider(): array
    {
        return [
            [
                'modelsData' => [
                    ['title' => 'Text to be searched'],
                    ['title' => 'Php Unit test diagnostic'],
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
                            'diagnosticCategoryId' => 0,
                            'status' => 'active',
                            'type' => 'system',
                        ],
                        [
                            'id' => 2,
                            'title' => 'Php Unit test diagnostic',
                            'description' => '',
                            'diagnosticCategoryId' => 0,
                            'status' => 'active',
                            'type' => 'system',
                        ],
                        [
                            'id' => 3,
                            'title' => 'another text',
                            'description' => '',
                            'diagnosticCategoryId' => 0,
                            'status' => 'active',
                            'type' => 'system',
                        ],
                    ],
                    'meta' => [
                        'pagination' => [
                            'total' => 3,
                            'count' => 3,
                            'per_page' => 15,
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
    public function deleteDataProvider(): array
    {
        return [
            [
                'data' => ['title' => '123'],
            ],
            [
                'data' => [
                    'title' => 'Php Unit test diagnostic',
                    'description' => 'Desc',
                    'diagnosticCategoryId' => 1,
                    'status' => 'disabled'
                ],
            ],
        ];
    }
    
    public function testCreateWithDiseases(): void
    {
        $response = $this->sendPost($this->getUri(), [
            'title' => 'phpunit title',
            'description' => 'phpunit description',
            'diseases' => factory(Disease::class, 3)->create()->toArray()
        ]);

        $response->assertStatus(201)->assertJson([
            'id' => 1,
            'title' => 'phpunit title',
            'description' => 'phpunit description',
            'diagnosticCategoryId' => 0,
            'status' => 'active',
            'diseases' => [
                [ 'id' => 1 ],
                [ 'id' => 2 ],
                [ 'id' => 3 ],
            ]
        ]);
    }
    
    public function testUpdateWithDiseases(): void
    {
        $diagnostic = factory(Diagnostic::class)->create([
            'title' => 'phpunit title',
            'description' => 'phpunit description',
            'diagnostic_category_id' => $category = factory(DiagnosticCategory::class)->create()->getAttribute('id'),
        ]);
        $diagnostic->diseases()->attach(
            factory(Disease::class, 4)->create()->get('id')
        );

        $response = $this->sendPut($this->getUri() . '/' . $diagnostic->getAttribute('id'), [
            'id' => $diagnostic->getAttribute('id'),
            'diseases' => [
                [ 'id' => $disease = factory(Disease::class)->create()->getAttribute('id') ]
            ],
        ]);
        $response->assertStatus(202)->assertJson([
            'id' => $diagnostic->getAttribute('id'),
            'title' => 'phpunit title',
            'description' => 'phpunit description',
            'diagnosticCategoryId' => $category,
            'diseases' => [
                [
                    'id' => $disease
                ],
            ],
            'status' => 'active',
            'type' => 'system',
        ]);
    }
}
