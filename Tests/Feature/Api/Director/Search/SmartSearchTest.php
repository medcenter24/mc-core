<?php
/*
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
 * Copyright (c) 2022 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace medcenter24\mcCore\Tests\Feature\Api\Director\Search;

use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class SmartSearchTest extends TestCase
{
    use DirectorTestTraitApi;

    public function testStoreSearcher(): void
    {
        $response = $this->sendPost('/api/director/smart-search', $this->getSearcherData());
        $response->assertStatus(201);
        $response->assertHeader('Location', url('/api/director/smart-search/1'));

        $response = $this->sendGet('/api/director/smart-search/1');
        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'body'  => $this->getSearcherData()['body'],
                'id'    => 1,
                'title' => 'Search Template 1',
                'type'  => 'searcher',
            ],
        ]);
    }

    #[ArrayShape([
        'title'   => "string",
        'type'    => "string",
        'body'    => "string",
    ])] private function getSearcherData(): array
    {
        return [
            'title' => 'Search Template 1',
            'type'  => 'searcher',
            'body'  => json_encode([
                'filters' => [
                    'cities'             => [
                        [
                            'id'           => 1,
                            'title'        => 'City',
                            'regionId'     => 1,
                            'regionTitle'  => 'Region',
                            'countryTitle' => 'Country',
                        ],
                    ],
                    'assistants'         => [
                        [
                            'id'      => 1,
                            'title'   => 'Assistant1',
                            'email'   => 'a1@e.com',
                            'comment' => '',
                            'refKey'  => 'a1',
                        ],
                        [
                            'id'      => 2,
                            'title'   => 'Assistant2',
                            'email'   => 'a2@e.com',
                            'comment' => '',
                            'refKey'  => 'A2',
                        ],
                    ],
                    'doctors'            => [
                        [
                            'id'                 => 1,
                            'name'               => 'a',
                            'description'        => 'c',
                            'refKey'             => 'b',
                            'userId'             => '0',
                            'medicalBoardNumber' => 'd',
                        ],
                    ],
                    'caseableTypes'      => [
                        'doctor',
                        'hospital',
                    ],
                    'patients'           => [
                        [
                            'id'       => 1,
                            'name'     => 'TEST-TESTOV',
                            'address'  => 'asdf',
                            'phones'   => '123',
                            'birthday' => '1212-12-12',
                            'comment'  => 'asdf',
                        ],
                    ],
                    'handlingTimeRanges' => [],
                    'accidentStatuses'   => [
                        [
                            'id'    => 1,
                            'title' => 'new',
                            'type'  => 'accident',
                        ],
                        [
                            'id'    => 2,
                            'title' => 'assigned',
                            'type'  => 'doctor',
                        ],
                        [
                            'id'    => 3,
                            'title' => 'in_progress',
                            'type'  => 'doctor',
                        ],
                    ],
                    'accidentTypes'      => [
                        [
                            'id'          => 1,
                            'title'       => 'insurance',
                            'description' => '',
                        ],
                        [
                            'id'          => 2,
                            'title'       => 'non-insurance',
                            'description' => '',
                        ],
                    ],
                    'visitTimeRanges'    => [],
                    'doctorServices'     => [
                        [
                            'id'          => 1,
                            'title'       => 'service1',
                            'description' => '',
                            'status'      => 'active',
                            'diseases'    => [],
                            'type'        => 'director',
                        ],
                    ],
                    'doctorSurveys'      => [
                        [
                            'id'          => 1,
                            'title'       => 'Survey1',
                            'description' => '',
                            'status'      => 'active',
                            'diseases'    => [],
                            'type'        => 'director',
                        ],
                    ],
                    'doctorDiagnostics'  => [
                        [
                            'id'                   => 1,
                            'title'                => 'diagnostic 1',
                            'description'          => '',
                            'diagnosticCategoryId' => 0,
                            'status'               => 'active',
                            'diseases'             => [],
                            'type'                 => 'director',
                        ],
                    ],
                ],
                'fields'  => [
                    'accidentId' => 0,
                    'doctorName' => 0,
                ],
            ]),
        ];
    }
}
