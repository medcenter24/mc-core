<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace tests\Unit\Dhv24;


use App\Helpers\Arr;
use App\Services\DocxReader\SimpleDocxReaderService;
use App\Services\DomDocumentService;
use App\Services\ExtractTableFromArrayService;
use App\Services\Import\Dhv24\Dhv24Docx2017Provider;
use Tests\SamplePath;
use Tests\TestCase;

class Dhv24FilesFormatTest extends TestCase
{
    use SamplePath;

    /**
     * @var SimpleDocxReaderService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = new SimpleDocxReaderService();
    }

    /**
     * dataProvider with documents
     * @return array
     */
    public function getDocuments()
    {
        return [
            [$this->getSamplePath() . 't1.docx'],
            [$this->getSamplePath() . 't2.docx'],
            [$this->getSamplePath() . 'FosterAbigail.DHV.docx'],
        ];
    }

    /**
     * Looking for images in document
     * @param $path
     * @dataProvider getDocuments
     */
    public function testImages($path)
    {
        $img = $this->service->load($path)->getImages();
        self::assertGreaterThanOrEqual(3, $img, 'In this document more than 3 images');
    }

    /**
     * Check that data format could be been used by importer provider
     * @param $path
     * @dataProvider getDocuments
     */
    public function testRequiredPoints($path)
    {
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

        // title
        $header = Arr::multiArrayToString($tables[0]);
        self::assertStringStartsWith('MEDICAL SERVICES NETWORK IN SPAIN, ANDORRA', $header);

        // consists of the assistance, patient, referral number
        $firstTableContainer = $tableExtractorService->extract($tables[1][2]);
        $firstTable = current($firstTableContainer[ExtractTableFromArrayService::TABLES]);
        $assistantInfo = current(array_shift($firstTable));
        $this->assertEquals(2, count($assistantInfo), 'Assistant info includes 2 arrays');

        $assistantMarker = Arr::multiArrayToString(array_shift($assistantInfo));
        $this->assertEquals('A cargo de compañia', $assistantMarker);

        // patient
        $caseInfoTable = array_map(function ($val1) {
            return array_map(function ($val2) {
                return Arr::multiArrayToString($val2);
            }, $val1);
        }, $firstTable);

        // name, ref_num, dhv_ref_num
        $caseInfoArray = Arr::collectTableRows($caseInfoTable);

        $keys = array_keys($caseInfoArray);
        self::assertEquals([
            'P aciente , fecha de nacimiento',
            'Assistance Ref. num.',
            'Ref.num. Doctor Home Visit',
        ], $keys, 'Patient data is correct');

        // investigation table
        $investigations = $tables[1][3][0];
        // reason, condition, addition, advices
        $mergedInvestigations = array_map(function ($row) {
            return Arr::multiArrayToString($row);
        }, $investigations);

        $mergedInvestigations = array_values($mergedInvestigations);
        $this->assertGreaterThanOrEqual(4, $mergedInvestigations);

        $diagnostico = $tableExtractorService->extract($tables[1][4][0]);
        $checkPoint = Arr::multiArrayToString($diagnostico[ExtractTableFromArrayService::TABLES][0][0]);
        self::assertStringStartsWith('D  I  A  G  N  O  S  T  I  C  O', $checkPoint, 'Diagnostic on the right place');

        $checkPoint = Arr::multiArrayToString($tables[1][5][0]);
        if ($checkPoint == 'Н аименование услуги , С on cept') {
            // without Doctor
            self::assertTrue(true, 'Services found');
        } else {
            self::assertTrue(isset($tables[1][6][0]));
            // with Doctor
            self::assertEquals('Н аименование услуги , С on cept', Arr::multiArrayToString($tables[1][6][0]), 'Services found');
        }

        $total = [];
        foreach ($tables[3][0] as $col) {
            $total[] = Arr::multiArrayToString($col);
        }

        self::assertEquals('TOTAL IMPORT, EUR', $total[1]);

        // date, place, visit time
        $orgInfoTitle = Arr::multiArrayToString($tables[4][0][0]);
        self::assertNotEmpty($orgInfoTitle, 'Org info has been provided');
        $orgInfoValue = Arr::multiArrayToString($tables[4][0][1]);
        self::assertNotEmpty($orgInfoValue, 'Org info has been provided');
    }
}
