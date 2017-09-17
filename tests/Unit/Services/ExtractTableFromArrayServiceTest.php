<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services;

use App\Services\ExtractTableFromArrayService;
use Tests\TestCase;

class ExtractTableFromArrayServiceTest extends TestCase
{
    /**
     * @var ExtractTableFromArrayService
     */
    private $service;

    protected function setUp()
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
