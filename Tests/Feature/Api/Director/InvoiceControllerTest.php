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

use medcenter24\mcCore\App\Services\Entity\InvoiceService;
use medcenter24\mcCore\App\Services\Entity\PaymentService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorApiModelTest;

class InvoiceControllerTest extends DirectorApiModelTest
{

    private const URI = 'api/director/invoice';

    private InvoiceService $invoiceService;
    private PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceService = new InvoiceService();
        $this->paymentService = new PaymentService();
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
        return InvoiceService::class;
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
                        'type' => ['The type field is required.']
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
                    'type' => 'form',
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'type' => 'form',
                ],
            ],
            [
                'data' => [
                    'type' => 'Php Unit test',
                    'title' => 'aaa',
                    'status' => 'new',
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'type' => 'Php Unit test',
                    'title' => 'aaa',
                    'status' => 'new',
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
                    'type' => 'form',
                    'title' => 'test',
                    'payment_id' => 0,
                    'status' => 'new',
                ],
                'updateData' => [
                    'id' => 1,
                    'type' => 'Php Unit test',
                    'title' => 'aaa',
                    'status' => 'paid',
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'type' => 'Php Unit test',
                    'title' => 'aaa',
                    'status' => 'paid',
                    'price' => 0,
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

    public function testSearchData(): void
    {
        $invoice = $this->invoiceService->create([
            InvoiceService::FIELD_PAYMENT_ID => $this->paymentService->create([
                PaymentService::FIELD_VALUE => 5,
            ])->id,
        ]);

        $response = $this->sendPost(self::URI .'/search', [
            'filter' => [
                'fields' => [
                    [
                        'elType' => 'text',
                        'field' => 'id',
                        'match' => 'eq',
                        'value' => $invoice->id,
                    ],
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJson(['data' => [
                [
                    'id' => 1,
                    'title' => '',
                    'type' => 'upload',
                    'status' => 'new',
                    'price' => 5,
                ]
            ],
                'meta' => [
                    'pagination' => [
                        'total' => 1,
                        'count' => 1,
                        'per_page' => 25,
                        'current_page' => 1,
                        'total_pages' => 1,
                        'links' => []
                    ]
                ]]);
    }
}
