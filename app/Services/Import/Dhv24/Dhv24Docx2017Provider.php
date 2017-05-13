<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Import\Dhv24;


use app\Services\DocxReader\DocxReaderInterface;
use App\Services\DocxReader\SimpleDocxReaderService;
use App\Services\DomDocumentService;
use App\Services\ExtractTableFromArrayService;
use App\Services\Import\DataProvider;

class Dhv24Docx2017Provider extends DataProvider
{
    /**
     * @var SimpleDocxReaderService
     */
    private $readerService;

    /**
     * @var ExtractTableFromArrayService
     */
    private $tableExtractorService;

    /**
     * @var DomDocumentService
     */
    private $domService;

    public function __construct(DocxReaderInterface $readerService = null, $tableExtractorService = null, $domService = null)
    {
        if ($readerService) {
            $this->readerService = $readerService;
        } else {
            $this->readerService = new SimpleDocxReaderService();
        }

        if ($tableExtractorService) {
            $this->tableExtractorService = $tableExtractorService;
        } else {
            $this->tableExtractorService = new ExtractTableFromArrayService([
                ExtractTableFromArrayService::CONFIG_TABLE => ['w:tbl'],
                ExtractTableFromArrayService::CONFIG_ROW => ['w:tr'],
                ExtractTableFromArrayService::CONFIG_CEIL => ['w:tc'],
            ]);
        }

        if ($domService) {
            $this->domService = $domService;
        } else {
            $this->domService = new DomDocumentService([
                DomDocumentService::STRIP_STRING => true,
                DomDocumentService::CONFIG_WITHOUT_ATTRIBUTES => true,
            ]);
        }
    }

    public function load($path = '')
    {
        $this->readerService->load($path);
        return $this;
    }

    public function check()
    {
        // point 1 main phrazes and their order
        $points = [
            'MEDICAL REPORT, INVOICE',
            'D  I  A  G  N  O  S  T  I  C  O',
            'Наименование услуги, Сoncept',
            'TOTAL IMPORT, EUR',
            'Дата,  место, время визита',
            'Fecha, lugar de visita SPAIN'
        ];

        foreach ($points as $checkPoint) {
            if (mb_strpos($this->readerService->getText(), $checkPoint) !== false) {
                return false;
            }
        }

        return true;
    }

    public function import()
    {
        $data = $this->tableExtractorService->extract($this->domService->toArray($this->readerService->getDom()));
        $tables = $data[ExtractTableFromArrayService::TABLES];
    }
}
