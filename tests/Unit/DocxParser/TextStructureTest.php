<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\DocxParser;

use App\Services\DocxReader\SimpleDocxReaderService;
use App\Services\Parser\SmartTextParser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TextStructureTest extends TestCase
{
    /**
     * Path to the folder with docx examples
     * @var string
     */
    private $samplePath = '';

    /**
     * @var SimpleDocxReaderService
     */
    private $service;

    protected function getSamplePath()
    {
        if (!$this->samplePath) {
            $this->samplePath = __DIR__ . DIRECTORY_SEPARATOR . 'samples';
        }

        return $this->samplePath;
    }

    /**
     * @return void
     */
    public function testRead()
    {
        $path = $this->getSamplePath() . DIRECTORY_SEPARATOR . 'structure.xml';
        $doc = new \DOMDocument();
        $doc->loadXML(file_get_contents($path), LIBXML_NOBLANKS | LIBXML_NOCDATA | LIBXML_NOEMPTYTAG | LIBXML_NSCLEAN | LIBXML_PARSEHUGE | LIBXML_PEDANTIC );

        $parser = new SmartTextParser();
        $parser->loadDom($doc);
        $arrayContent = $parser->getTextMap();
dd($arrayContent);
        $a = $doc->saveHTML();
        self::assertContains('NIF: B55570451', $this->getService()->getText(), 'This text is correct');
    }

    /**
     * Load new case from the docx
     * with media
     * and as result should be new case
     */
    public function testCaseLoader()
    {
        $path = $this->getSamplePath() . DIRECTORY_SEPARATOR . 'str';
        $service = $this->getService()->load($path);
        $dom = $service->getDom();

        $v = $this->dom_to_array($dom);

        $x = $dom->saveHTML();

        $b = $x;
    }

    function dom_to_array($root)
    {
        $result = array();

        if ($root->hasAttributes())
        {
            $attrs = $root->attributes;

            foreach ($attrs as $i => $attr)
                $result[$attr->name] = $attr->value;
        }

        $children = $root->childNodes;

        if ($children->length == 1)
        {
            $child = $children->item(0);

            if ($child->nodeType == XML_TEXT_NODE)
            {
                $result['_value'] = $child->nodeValue;

                if (count($result) == 1)
                    return $result['_value'];
                else
                    return $result;
            }
        }

        $group = array();

        for($i = 0; $i < $children->length; $i++)
        {
            $child = $children->item($i);

            if (!isset($result[$child->nodeName]))
                $result[$child->nodeName] = $this->dom_to_array($child);
            else
            {
                if (!isset($group[$child->nodeName]))
                {
                    $tmp = $result[$child->nodeName];
                    $result[$child->nodeName] = array($tmp);
                    $group[$child->nodeName] = 1;
                }

                $result[$child->nodeName][] = $this->dom_to_array($child);
            }
        }

        return $result;
    }

}
