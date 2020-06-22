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

namespace medcenter24\mcCore\Tests\Feature\Api\Director\Cases;

use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\CaseAccidentService;
use medcenter24\mcCore\App\Services\Entity\PatientService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorApiModelTest;

class CaseAccidentControllerTest extends DirectorApiModelTest
{
    private const URI = '/api/director/cases';

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
        return CaseAccidentService::class;
    }

    /**
     * @inheritDoc
     */
    public function failedDataProvider(): array
    {
        return [
            [
                'data' => [
                    'accident' => false,
                ],
                'expectedResponse' => [
                    'message' => '422 Unprocessable Entity',
                    'errors' =>
                        [
                            'accident' => [
                                'accident has to be an array',
                            ],
                        ],
                    'status_code' => 422,
                ],
            ],
            // all other errors are from the AccidentRequest and could be checked there
        ];
    }

    /**
     * @inheritDoc
     */
    public function createDataProvider(): array
    {
        return [
            [
                'data' => [],
                'expectedResponse' => [
                    'id' => 1,
                    'assistantId' => 0,
                    'repeated' => 0,
                    'assistantRefNum' => '',
                    'caseType' => 'doctor',
                    'symptoms' => '',
                    'handlingTime' => '',
                    'patientName' => '',
                    'checkpoints' => '',
                    'statusTitle' => 'new',
                    'cityTitle' => '',
                    'price' => 0,
                    'doctorsFee' => 0,
                ],
            ],
            [
                'data' => [
                    CaseAccidentService::PROPERTY_ACCIDENT => [],
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'assistantId' => 0,
                    'repeated' => 0,
                    'assistantRefNum' => '',
                    'caseType' => 'doctor',
                    'symptoms' => '',
                    'handlingTime' => '',
                    'patientName' => '',
                    'checkpoints' => '',
                    'statusTitle' => 'new',
                    'cityTitle' => '',
                    'price' => 0,
                    'doctorsFee' => 0,
                ],
            ],
            [
                'data' => [
                    'accident' => [
                        'caseableType' => 'hospital',
                        'symptoms' => 'phpunit symptoms',
                        'handlingTime' => '2031-08-20 02:11:11',
                    ],
                    'patient' => [
                        'name' => 'Php unit patient name',
                    ],
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'assistantId' => 0,
                    'repeated' => 0,
                    'assistantRefNum' => '',
                    'caseType' => 'hospital',
                    'symptoms' => 'phpunit symptoms',
                    'handlingTime' => '2031-08-20 02:11:11',
                    'patientName' => 'Php unit patient name',
                    'checkpoints' => '',
                    'statusTitle' => 'new',
                    'cityTitle' => '',
                    'price' => 0,
                    'doctorsFee' => 0,
                ],
            ]
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
                    CaseAccidentService::PROPERTY_ACCIDENT => [],
                ],
                'updateData' => [
                    CaseAccidentService::PROPERTY_ACCIDENT => [
                        'id' => 1,
                        'assistantId' => 0,
                        'parentId' => 0,
                        'symptoms' => 'phpunit symptoms',
                        'handlingTime' => '2031-08-20 02:11:11',
                    ],
                    CaseAccidentService::PROPERTY_PATIENT => [
                        'name' => 'Php unit patient name',
                    ],
                ],
                'expectedResponse' => [
                    'id' => 1,
                    'assistantId' => 0,
                    'repeated' => 0,
                    'assistantRefNum' => '',
                    'caseType' => 'doctor',
                    'symptoms' => 'phpunit symptoms',
                    'handlingTime' => '2031-08-20 02:11:11',
                    'patientName' => 'Php unit patient name',
                    'checkpoints' => '',
                    'statusTitle' => 'new',
                    'cityTitle' => '',
                    'price' => 0,
                    'doctorsFee' => 0,
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
                        CaseAccidentService::PROPERTY_ACCIDENT => [
                            AccidentService::FIELD_CASEABLE_TYPE => HospitalAccident::class,
                            AccidentService::FIELD_ASSISTANT_ID => 3,
                            AccidentService::FIELD_SYMPTOMS => 'phpunit symptoms2',
                            AccidentService::FIELD_HANDLING_TIME => '2031-08-20 02:11:11',
                        ],
                        CaseAccidentService::PROPERTY_PATIENT => [
                            PatientService::FIELD_NAME => 'Php unit patient name3',
                        ],
                    ],
                    [
                        CaseAccidentService::PROPERTY_ACCIDENT => [
                            AccidentService::FIELD_CASEABLE_TYPE => HospitalAccident::class,
                            AccidentService::FIELD_ASSISTANT_ID => 3,
                            AccidentService::FIELD_SYMPTOMS => 'phpunit symptoms3',
                            AccidentService::FIELD_HANDLING_TIME => '2031-08-20 02:11:11',
                        ],
                        CaseAccidentService::PROPERTY_PATIENT => [
                            PatientService::FIELD_NAME => 'Php unit patient name1',
                        ],
                    ],
                    [
                        CaseAccidentService::PROPERTY_ACCIDENT => [
                            AccidentService::FIELD_CASEABLE_TYPE => HospitalAccident::class,
                            AccidentService::FIELD_ASSISTANT_ID => 3,
                            AccidentService::FIELD_SYMPTOMS => 'phpunit symptoms4',
                            AccidentService::FIELD_HANDLING_TIME => '2031-08-20 02:11:11',
                        ],
                        CaseAccidentService::PROPERTY_PATIENT => [
                            PatientService::FIELD_NAME => 'Php unit patient name2',
                        ],
                    ],
                ],
                // filters
                [
                    'filters' => [],
                ],
                // response
                'expectedResponse' => [
                    'data' => [

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
                'data' => [
                    CaseAccidentService::PROPERTY_ACCIDENT => [],
                ],
                'response' => [
                    'data' => [
                        'id' => 1,
                        'assistantId' => 0,
                        'repeated' => 0,
                        'assistantRefNum' => '',
                        'caseType' => 'doctor',
                        'symptoms' => '',
                        'handlingTime' => '',
                        'patientName' => '',
                        'checkpoints' => '',
                        'statusTitle' => 'new',
                        'cityTitle' => '',
                        'price' => 0,
                        'doctorsFee' => 0,
                    ],
                ],
            ],
            [
                'data' => [
                    CaseAccidentService::PROPERTY_ACCIDENT => [
                        AccidentService::FIELD_CASEABLE_TYPE => HospitalAccident::class,
                        AccidentService::FIELD_ASSISTANT_ID => 3,
                        AccidentService::FIELD_SYMPTOMS => 'phpunit symptoms2',
                        AccidentService::FIELD_HANDLING_TIME => '2031-08-20 02:11:11',
                    ],
                    CaseAccidentService::PROPERTY_PATIENT => [
                        PatientService::FIELD_NAME => 'Php unit patient name3',
                    ],
                ],
                'response' => [
                    'data' => [
                        'id' => 1,
                        'assistantId' => 3,
                        'repeated' => 0,
                        'assistantRefNum' => '',
                        'caseType' => 'hospital',
                        'symptoms' => 'phpunit symptoms2',
                        'handlingTime' => '2031-08-20 02:11:11',
                        'patientName' => 'Php unit patient name3',
                        'checkpoints' => '',
                        'statusTitle' => 'new',
                        'cityTitle' => '',
                        'price' => 0,
                        'doctorsFee' => 0,
                    ],
                ],
            ],
            [
                'data' => [
                    CaseAccidentService::PROPERTY_ACCIDENT => [
                        AccidentService::FIELD_CASEABLE_TYPE => DoctorAccident::class,
                        AccidentService::FIELD_ASSISTANT_ID => 3,
                        AccidentService::FIELD_SYMPTOMS => 'phpunit symptoms3',
                        AccidentService::FIELD_HANDLING_TIME => '2031-08-20 02:11:11',
                        AccidentService::FIELD_REF_NUM => 'PHPUNIT REF NUM',
                        AccidentService::FIELD_PARENT_ID => 2,
                    ],
                    CaseAccidentService::PROPERTY_PATIENT => [
                        PatientService::FIELD_NAME => 'Php unit patient name1',
                    ],
                ],
                'expectedResponse' => [
                    'data' => [
                        'id' => 1,
                        'assistantId' => 3,
                        'repeated' => 2,
                        'assistantRefNum' => '',
                        'refNum' => 'PHPUNIT REF NUM',
                        'caseType' => 'doctor',
                        'symptoms' => 'phpunit symptoms3',
                        'handlingTime' => '2031-08-20 02:11:11',
                        'patientName' => 'Php unit patient name1',
                        'checkpoints' => '',
                        'statusTitle' => 'new',
                        'cityTitle' => '',
                        'price' => 0,
                        'doctorsFee' => 0,
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
                'data' => [
                    CaseAccidentService::PROPERTY_ACCIDENT => [],
                ],
            ],
            [
                'data' => [
                    CaseAccidentService::PROPERTY_ACCIDENT => [
                        AccidentService::FIELD_CASEABLE_TYPE => HospitalAccident::class,
                        AccidentService::FIELD_ASSISTANT_ID => 3,
                        AccidentService::FIELD_PARENT_ID => 2,
                        AccidentService::FIELD_SYMPTOMS => 'phpunit symptoms',
                        AccidentService::FIELD_HANDLING_TIME => '2031-08-20 02:11:11',
                    ],
                    CaseAccidentService::PROPERTY_PATIENT => [
                        PatientService::FIELD_NAME => 'Php unit patient name',
                    ],
                ],
            ]
        ];
    }
}
