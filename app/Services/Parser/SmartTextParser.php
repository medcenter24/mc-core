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

namespace App\Services\Parser;


use App\Services\Parser\Helpers\DomTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use tests\Unit\TextParser\TextParserException;

class SmartTextParser
{

    use DomTrait;

    /**
     * Data type table
     */
    const TABLES = 'tables';

    /**
     * Data type all other
     */
    const CONTENT = 'content';

    private $config = [
        TableHelper::TABLE_TAG => ['table'],
        TableHelper::TABLE_ROW => ['tr'],
        TableHelper::TABLE_CEIL => ['th', 'td'],
        TableHelper::FIRST_INDEX_ROW => true
    ];

    /**
     * @var \DOMElement
     */
    private $dom;

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    public function loadDom(\DOMDocument $document)
    {
        $this->dom = $document;
    }

    public function getTextMap()
    {
        if (!is_a($this->dom, \DOMDocument::class)) {
            throw new ModelNotFoundException('DomDocument should be provided with loadDom($dom) method');
        }

        return $this->parse($this->dom);
    }

    private function parse(\DOMNode $node)
    {
        $array = $this->domToArray($node, false, true);
        $result = $this->extractTables($array);
        return $result;
    }

    /**
     * extract all tables to the tables section
     *
     * @param array $array
     * @return array
     */
    private function extractTables(array $array)
    {
        $result = [
            self::TABLES => [],
            self::CONTENT => []
        ];
        foreach ($array as $key => $body) {
            $key .= ''; // make it string
            if (in_array($key, $this->getConfig(self::TABLE_TAG))) {
                $result[self::TABLES][] = TableHelper::getKeyValueFromArray($body);
            } else {
                if (is_array($body)) {
                    $container = $this->extractTables($body);
                    $result[self::TABLES] += $container[self::TABLES];
                    $result[self::CONTENT][$key] = $container[self::CONTENT];
                } else {
                    $result[self::CONTENT][$key] = $body;
                }
            }
        }
        return $result;
    }

    private function parseTable(array $array)
    {
        $res = [];
        $i = 0; // interaction
        foreach ($array as $row) {
        }

    }

    private function getConfig($key = '')
    {
        if (!isset($this->config[$key])) {
            throw new TextParserException('Key Not Found: ' . $key);
        }

        return $this->config[$key];
    }
}
