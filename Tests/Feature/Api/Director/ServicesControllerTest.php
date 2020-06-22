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

use medcenter24\mcCore\App\Entity\Disease;
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Filter;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Paginator;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Sorter;
use medcenter24\mcCore\App\Services\Core\Http\DataLoaderRequestBuilder;
use medcenter24\mcCore\App\Services\Entity\ServiceService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorApiModelTest;

class ServicesControllerTest extends DirectorApiModelTest
{
    private const URI = 'api/director/services';

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
        return ServiceService::class;
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
                'data' => [ServiceService::FIELD_TITLE => ''],
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
                'data' => [ServiceService::FIELD_TITLE => '1'],
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
    public function showDataProvider(): array
    {
        return [
            [
                'data' => [ServiceService::FIELD_TITLE => '123'],
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
                    ServiceService::FIELD_TITLE => 'Php Unit test',
                    ServiceService::FIELD_DESCRIPTION => 'Desc',
                    ServiceService::FIELD_STATUS => 'disabled'
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
        $service = factory(Service::class)->create([
            'title' => 'phpunit title',
            'description' => 'phpunit description',
        ]);
        $service->diseases()->attach(
            factory(Disease::class, 4)->create()->get('id')
        );

        $response = $this->sendPut($this->getUri() . '/' . $service->getAttribute('id'), [
            'id' => $service->getAttribute('id'),
            'diseases' => [
                [ 'id' => $disease = factory(Disease::class)->create()->getAttribute('id') ]
            ],
        ]);
        $response->assertStatus(202)->assertJson([
            'id' => $service->getAttribute('id'),
            'title' => 'phpunit title',
            'description' => 'phpunit description',
            'diseases' => [
                [
                    'id' => $disease
                ],
            ],
            'status' => 'active',
            'type' => 'system',
        ]);
    }

    public function testSearchFiltered(): void
    {
        // 1
        factory(Service::class)->create([
            ServiceService::FIELD_TITLE => '3 service 1 phpunit',
            ServiceService::FIELD_STATUS => ServiceService::STATUS_ACTIVE,
        ]);
        // 2
        factory(Service::class)->create([
            ServiceService::FIELD_TITLE => '1 service 2 phpunit',
            ServiceService::FIELD_STATUS => ServiceService::STATUS_DISABLED
        ]);
        // 3
        factory(Service::class)->create([
            ServiceService::FIELD_TITLE => '2 service 3 phpunit',
            ServiceService::FIELD_STATUS => ServiceService::STATUS_HIDDEN,
        ]);
        // 4
        factory(Service::class)->create([
            ServiceService::FIELD_TITLE => '3 service 1 phpunit',
            ServiceService::FIELD_STATUS => ServiceService::STATUS_ACTIVE,
        ]);
        // 5
        factory(Service::class)->create([
            ServiceService::FIELD_TITLE => '5 service 1 phpunit',
            ServiceService::FIELD_STATUS => ServiceService::STATUS_ACTIVE,
        ]);
        // 6
        factory(Service::class)->create([
            ServiceService::FIELD_TITLE => '8 service 1 phpunit',
            ServiceService::FIELD_STATUS => ServiceService::STATUS_ACTIVE,
        ]);
        // 7
        factory(Service::class)->create([
            ServiceService::FIELD_TITLE => '9 service 1 phpunit',
            ServiceService::FIELD_STATUS => ServiceService::STATUS_ACTIVE,
        ]);

        $response = $this->sendPost(self::URI . '/search', [
            DataLoaderRequestBuilder::FILTER => [
                Filter::FIELDS => [
                    [
                        Filter::FIELD_NAME => ServiceService::FIELD_STATUS,
                        Filter::FIELD_MATCH => Filter::MATCH_EQ,
                        Filter::FIELD_VALUE => ServiceService::STATUS_ACTIVE,
                        Filter::FIELD_EL_TYPE => Filter::TYPE_TEXT,
                    ],
                ],
            ],
            DataLoaderRequestBuilder::SORTER => [
                Sorter::FIELDS => [
                    [
                        Sorter::FIELD_NAME => ServiceService::FIELD_TITLE,
                        Sorter::FIELD_VALUE => 'desc',
                    ],
                    [
                        Sorter::FIELD_NAME => ServiceService::FIELD_ID,
                        Sorter::FIELD_VALUE => 'asc',
                    ],
                ],
            ],
            DataLoaderRequestBuilder::PAGINATOR => [
                Paginator::FIELDS => [
                    [
                        Paginator::FIELD_NAME => Paginator::FIELD_LIMIT,
                        Paginator::FIELD_VALUE => 3,
                    ],
                    [
                        Paginator::FIELD_NAME => Paginator::FIELD_OFFSET,
                        Paginator::FIELD_VALUE => 3,
                    ]
                ]
            ]
        ]);

        $response->assertOk()->assertJson([
            'data' => [
                // order by title desc
                /*
                 * offset 3
                 * [
                    'id' => 7,
                    'title' => '9 service 1 phpunit',
                ],
                [
                    'id' => 6,
                    'title' => '8 service 1 phpunit',
                ],
                [
                    'id' => 5,
                    'title' => '5 service 1 phpunit'
                ],
                */
                // order by id asc
                [
                    'id' => 1,
                    'title' => '3 service 1 phpunit',
                ],
                [
                    'id' => 4,
                    'title' => '3 service 1 phpunit',
                ],
            ],
            'meta' => [
                'pagination' => [
                        'total' => 5,
                        'count' => 2,
                        'per_page' => 3,
                        'current_page' => 2,
                        'total_pages' => 2,
                        'links' => [
                    ],
                ],
            ],
        ]);
    }
}
