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

namespace Tests\Unit\TextParser;


use App\Services\Parser\Helpers\DomTrait;

/**
 *
 * Class DomHelpersTest
 * @package Tests\Unit\TextParser
 */
class DomHelpersTest extends TextParser
{
    use DomTrait;

    public function testParseSimpleBody()
    {
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
        ], $this->domToArray($this->getDom(), false, true));

    }
}
