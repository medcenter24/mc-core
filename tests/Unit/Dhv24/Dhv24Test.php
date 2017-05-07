<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Dhv24;

use App\Helpers\Arr;
use App\Services\DocxReader\SimpleDocxReaderService;
use App\Services\DomDocumentService;
use App\Services\ExtractTableFromArrayService;
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

    public function setUp()
    {
        parent::setUp();
        $this->service = new SimpleDocxReaderService();
    }

    protected function getSamplePath()
    {
        if (!$this->samplePath) {
            $this->samplePath = __DIR__ . DIRECTORY_SEPARATOR . 'samples';
        }

        return $this->samplePath;
    }

    /**
     * Looking for images in document
     */
    public function testImages()
    {
        $path = $this->getSamplePath() . DIRECTORY_SEPARATOR . 't1.docx';
        $img = $this->service->load($path)->getImages();
        self::assertCount(4, $img, 'In this document 4 images');
    }

    /**
     * @return void
     */
    public function testRead()
    {
        $path = $this->getSamplePath() . DIRECTORY_SEPARATOR . 't1.docx';
        $this->service->load($path);
        self::assertContains('NIF: B55570451', $this->service->getText(), 'This text is correct');
    }

    /**
     * Load new case from the docx
     * with media
     * and as result should be new case
     */
    public function testCaseLoader()
    {
        $path = $this->getSamplePath() . DIRECTORY_SEPARATOR . 't1.docx';
        $service = $this->service->load($path);

        // it should be report invoice
        self::assertContains('MEDICAL REPORT, INVOICE', $this->service->getText(), 'This is correct invoice');

        $dom = $service->getDom();

        $domService = new DomDocumentService([
            DomDocumentService::STRIP_STRING => true,
            DomDocumentService::CONFIG_WITHOUT_ATTRIBUTES => true,
        ]);

        $arrayDocument = $domService->toArray($dom);

        $tableExtractorService = new ExtractTableFromArrayService([
            ExtractTableFromArrayService::CONFIG_TABLE => ['w:tbl'],
            ExtractTableFromArrayService::CONFIG_ROW => ['w:tr'],
            ExtractTableFromArrayService::CONFIG_CEIL => ['w:tc'],
        ]);

        $data = $tableExtractorService->extract($arrayDocument);
        $this->assertEquals(5, count($data[ExtractTableFromArrayService::TABLES]), 'Document has 5 tables on the top');

        $tables = $data[ExtractTableFromArrayService::TABLES];

        // consists of the assistance, patient, referral number
        $firstTableContainer = $tableExtractorService->extract($tables[1][2]);
        $firstTable = current($firstTableContainer[ExtractTableFromArrayService::TABLES]);
        $assistantInfo = current(array_shift($firstTable));
        $this->assertEquals(2, count($assistantInfo), 'Assistant info includes 2 arrays');

        $assistantMarker = Arr::multiArrayToString(array_shift($assistantInfo));
        $this->assertEquals('A cargo de compañia', $assistantMarker);

        // assistant information for case
        $assistant = Arr::multiArrayToString(array_shift($assistantInfo));

        $v = $firstTable;
    }
}
