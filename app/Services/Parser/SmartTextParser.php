<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Parser;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use tests\Unit\TextParser\TextParserException;

class SmartTextParser
{

    use
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
        $array = DomHelper::domToArray($node, false, true);
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
