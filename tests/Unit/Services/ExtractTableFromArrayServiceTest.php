<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
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

namespace Tests\Unit\Services;

use medcenter24\mcCore\App\Services\ExtractTableFromArrayService;
use Tests\TestCase;

class ExtractTableFromArrayServiceTest extends TestCase
{
    /**
     * @var ExtractTableFromArrayService
     */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExtractTableFromArrayService();
    }

    public function testExtractor()
    {
        $table = ['tableMarker' => ['thead' => ['trMarker' => ['thMarker' => 'key']], 'trMarker' => ['td' => 'value']]];
        $expected = [
            ExtractTableFromArrayService::TABLES => [0 => [['key'],['value']]],
            ExtractTableFromArrayService::CONTENT => []
        ];

        $this->service->setOptions([
            ExtractTableFromArrayService::CONFIG_TABLE => ['tableMarker'],
            ExtractTableFromArrayService::CONFIG_ROW => ['trMarker'],
            ExtractTableFromArrayService::CONFIG_CEIL => ['thMarker', 'td'],
        ]);

        $this->assertEquals($expected, $this->service->extract($table));
    }

    public function testExtractorMultiRows()
    {
        $table = [
            'tableMarker:0' => [
                'trMarker:0' => ['thMarker:0' => 'key0_1', 'thMarker:1' => 'key1_1', 'thMarker:2' => 'key2_1'],
                'trMarker:1' => ['td:0' => 'value0_1', 'td:1' => 'value1_1']
            ],
            'tableMarker:1' => [
                'trMarker:0' => ['thMarker:0' => 'key0_2', 'thMarker:1' => 'key1_2', 'thMarker:2' => 'key2_2'],
                'trMarker:1' => ['td:0' => 'value0_2', 'td:1' => 'value1_2']
            ]
        ];
        $expected = [
            ExtractTableFromArrayService::TABLES => [0 => [
                [
                    0 => 'key0_1',
                    1 => 'key1_1',
                    2 => 'key2_1',
                ],
                [
                    0 => 'value0_1',
                    1 => 'value1_1',
                ]
            ], 1 => [
                [
                    0 => 'key0_2',
                    1 => 'key1_2',
                    2 => 'key2_2',
                ],
                [
                    0 => 'value0_2',
                    1 => 'value1_2',
                ]
            ]],
            ExtractTableFromArrayService::CONTENT => []
        ];

        $this->service->setOptions([
            ExtractTableFromArrayService::CONFIG_TABLE => ['tableMarker'],
            ExtractTableFromArrayService::CONFIG_ROW => ['trMarker'],
            ExtractTableFromArrayService::CONFIG_CEIL => ['thMarker', 'td'],
        ]);

        $this->assertEquals($expected, $this->service->extract($table));
    }
}
