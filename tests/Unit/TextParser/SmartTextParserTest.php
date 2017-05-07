<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */


namespace Tests\Unit\TextParser;


use App\Services\Parser\SmartTextParser;


/**
 * Class SmartTextParserTest
 * @package Tests\Unit\TextParser
 */
class SmartTextParserTest extends TextParser
{
    /**
     * @var SmartTextParser
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = new SmartTextParser();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSmartTextParser()
    {
        $this->service->loadDom($this->getDom());
        self::assertEquals([
            'tables' => [
                [
                    [
                        'title' => 'key1',
                        'values' => ['val1.1', 'val1.2']
                    ],
                    [
                        'title' => 'key2',
                        'values' => ['val2.1', 'val2.2']
                    ]
                ],
                [
                    0 => [
                        'title' => 'User FirstName',
                        'values' => ['Foster']
                    ],
                    1 => [
                        'title' => 'User LastName',
                        'values' => 'Abigail'
                    ],
                    2 => [
                        'title' => 'ID',
                        'values' => ['1']
                    ],
                    3 => [
                        'title' => '',
                        'values' => ['Delete']
                    ]
                ],
            ],
            'body' => [
                'main' => [
                    'div' => [
                        0 => [
                            'div' => [
                                'div' => [
                                    'text-tag' => [
                                        '#text' => 'Something very that I need to get',
                                        'span' => 'Another text, stripped from main',
                                        'div' => [
                                            'div' => []
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        1 => [
                            'div' => []
                        ]
                    ]
                ]
            ]
        ], $this->service->getTextMap());
    }
}
