<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\DocxParser;

use App\Services\DocxReader\SimpleDocxReaderService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class Dhv24Test extends TestCase
{
    use DatabaseMigrations;

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
            $this->samplePath = __DIR__ . DIRECTORY_SEPARATOR . 'dhv24';
        }

        return $this->samplePath;
    }

    protected function getService()
    {
        if (!$this->service){
            $this->service = new SimpleDocxReaderService();
        }

        return $this->service;
    }

    /**
     * Looking for images in document
     */
    public function testImages()
    {
        $path = $this->getSamplePath() . DIRECTORY_SEPARATOR . 't1.docx';
        $img = $this->getService()->load($path)->getImages();
        self::assertCount(4, $img, 'In this document 4 images');
    }

    /**
     * @return void
     */
    public function testRead()
    {
        $path = $this->getSamplePath() . DIRECTORY_SEPARATOR . 't1.docx';
        $this->getService()->load($path);
        self::assertContains('NIF: B55570451', $this->getService()->getText(), 'This text is correct');
    }

    /**
     * Load new case from the docx
     * with media
     * and as result should be new case
     */
    public function testCaseLoader()
    {
        $path = $this->getSamplePath() . DIRECTORY_SEPARATOR . 't1.docx';
        $service = $this->getService()->load($path);
        $dom = $service->getDom();

        $v = $this->dom_to_array($dom);

        $x = $dom->saveHTML();

        $b = $x;
    }
}
