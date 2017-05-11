<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Import\Dhv24;


use App\Services\DocxReader\SimpleDocxReaderService;
use App\Services\Import\DataProvider;

class Dhv24Docx2017Provider extends DataProvider
{
    /**
     * @var SimpleDocxReaderService
     */
    private $readerService;

    public function load($path = '')
    {
        $readerService = new SimpleDocxReaderService();
        $readerService->load($path);
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
        mb_strpos($this->readerService->getText(), );
    }
}
