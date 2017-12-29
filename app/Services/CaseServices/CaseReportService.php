<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\CaseServices;

use App\Accident;
use App\Models\CaseReport;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class CaseReportService
{
    /**
     * Filesystem constants
     */
    const PDF_DISK = 'caseReportPdf';
    const PDF_FOLDER = 'pdfCaseReports';

    /**
     * @var CaseReport
     */
    private $report;

    public function generate(Accident $accident)
    {
        $this->report = new CaseReport($accident);
        return $this;
    }

    public function toHtml() {
        // todo make it works through accident->formReport component to store all the datas in the storage
        // I prefer to implement it as a new driver/option to the view which maybe easiest?
        return view('case.report', ['report' => $this->report]);
    }

    public function toPdf()
    {
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($this->toHtml());
        $mpdf->Output(\Storage::disk(self::PDF_DISK)->path($this->getUniquePdfFileName()), Destination::FILE);
    }

    public function getPdfPath()
    {
        $fileName = $this->getUniquePdfFileName();
        if (!\Storage::disk(self::PDF_DISK)->exists($fileName)) {
            $this->toPdf();
        }

        return \Storage::disk(self::PDF_DISK)->path($this->getUniquePdfFileName());
    }

    public function getUniquePdfFileName()
    {
        return $this->report->uniqueIdentifier() . '.pdf';
    }
}
