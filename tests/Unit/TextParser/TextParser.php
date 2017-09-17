<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\TextParser;


use Tests\TestCase;

class TextParser extends TestCase
{
    /**
     * @var \DOMDocument
     */
    private $dom;

    public function setUp()
    {
        parent::setUp();
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'structure.xml';
        $this->dom = new \DOMDocument();
        $this->dom->loadXML(file_get_contents($path), LIBXML_NOBLANKS | LIBXML_NOCDATA | LIBXML_NOEMPTYTAG | LIBXML_NSCLEAN | LIBXML_PARSEHUGE | LIBXML_PEDANTIC );
    }

    protected function getDom()
    {
        return $this->dom;
    }
}
