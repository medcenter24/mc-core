<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Parser;


use App\Services\DomDocumentService;

/**
 * Parse DOMDocument to array
 * could be used configurable parameters
 *
 * Class DomToArrayTest
 * @package Tests\Feature\Parser
 */
class DomToArrayTest extends DomTestCase
{

    /**
     * @var DomDocumentService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = new DomDocumentService();
    }

    public function testParseSimpleBody()
    {
        $this->service->setOptions([
            DomDocumentService::STRIP_STRING => true,
            DomDocumentService::CONFIG_WITHOUT_ATTRIBUTES => true,
        ]);

        self::assertEquals([
            'main' => [
                'div:0' => [
                    0 => [
                        'div' => [
                            'div' => [
                                'text-tag' => [
                                    '#text' => 'Something very that I need to get',
                                    'span' => 'Another text, stripped from main',
                                    'div' => [
                                        'div' => [
                                            'table' => [
                                                'tr:0' => [
                                                    [
                                                        'th:0' => [
                                                            0 => 'key1',
                                                        ],
                                                        'th:1' => [
                                                            0 => 'key2'
                                                        ]
                                                    ],
                                                ],
                                                'tr:1' => [
                                                    [
                                                        'td:0' => [
                                                            0 => 'val1.1'
                                                        ],
                                                        'td:1' => [
                                                            0 => 'val2.1'
                                                        ]
                                                    ],
                                                ],
                                                'tr:2' => [
                                                    [
                                                        'td:0' => [
                                                            0 => 'val1.2'
                                                        ],
                                                        'td:1' => [
                                                            0 => 'val2.2'
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],],
                'div:1' => [
                    0 => [
                        'div' => [
                            'table' => [
                                'thead' => [
                                    'tr' => [
                                        'th:0' => [
                                            0 => 'User FirstName'
                                        ],
                                        'th:1' => [
                                            0 => 'User LastName'
                                        ],
                                        'th:2' => [
                                            0 => 'ID'
                                        ]
                                    ]
                                ],
                                'tbody' => [
                                    'tr' => [
                                        'td:0' => [
                                            0 => 'Foster'
                                        ],
                                        'td:1' => [
                                            0 => 'Abigail'
                                        ],
                                        'td:2' => [
                                            0 => '1'
                                        ],
                                        'td:3' => [
                                            0 => [
                                                'btn' => 'Delete'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ], $this->service->toArray($this->getDom()));
    }
}
