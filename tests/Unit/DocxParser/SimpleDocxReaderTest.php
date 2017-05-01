<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\DocxParser;

use App\Services\DocxReader\SimpleDocxReaderService;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SimpleDocxReaderTest extends TestCase
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

    protected function getService()
    {
        if (!$this->service){
            $this->service = new SimpleDocxReaderService();
        }

        return $this->service;
    }

    /**
     * @param string $filePath
     * @dataProvider getSamples
     * @return void
     */
    public function testRead($filePath = '')
    {
        $this->getService()->load($filePath);
        self::assertContains('Computer science and informatics', $this->getService()->getText(), 'This text is correct');
    }

    /**
     * Array with files for test
     * @return array
     */
    public function getSamples()
    {
        $directory = new RecursiveDirectoryIterator($this->getSamplePath());
        $iterator = new RecursiveIteratorIterator($directory);
        $regEx = new RegexIterator($iterator, '/^.+\.docx$/i', RecursiveRegexIterator::GET_MATCH);
        $files = [];
        foreach ($regEx as $file) {
            $files[] = $file;
        }

        return $files;
    }

    /**
     * test file iterator
     */
    public function testIterator()
    {
        $directory = new RecursiveDirectoryIterator($this->getSamplePath());
        $iterator = new RecursiveIteratorIterator($directory);
        $regEx = new RegexIterator($iterator, '/^.+\.docx$/ui', RecursiveRegexIterator::ALL_MATCHES);
        $cnt = 0;
        foreach ($regEx as $item) {
            $cnt++;
        }

        self::assertEquals(3, $cnt, 'Files counted correctly');
    }
}
