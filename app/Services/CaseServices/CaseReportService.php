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

    public function htmlDocuments() {
        return view('case.documents', ['report' => $this->report]);
    }

    public function toPdf()
    {
        try {
            $mpdf = new Mpdf([
                'debug' => true,
                'useSubstitutions' => false,
                'simpleTables' => true,
                'use_kwt' => true,
                'shrink_tables_to_fit' => 1,
                'showImageErrors' => true,
                /*'allowCJKorphans' => false,
                'allowCJKoverflow' => true,*/
                /*'ignore_table_percents' => true,
                'ignore_table_widths' => true,*/
                /*'keepColumns' => true,
                'keep_table_proportions' => true,*/
                // 'justifyB4br' => true,
                'margin_left' => 3,
                'margin_right' => 5,
                'margin_top' => 9,
                'margin_bottom' => 1,
                'margin_header' => 0,
                'margin_footer' => 0,
            ]);
            $mpdf->WriteHTML($this->toHtml());

            $mpdf->SetTitle('Report ' . $this->report->uniqueIdentifier());
            $mpdf->SetAuthor("MyDoctors24.com");
            /*$mpdf->SetWatermarkText("Paid");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.1;*/
            $mpdf->AddPage();

            $mpdf->WriteHTML($this->documentsHtml());

            $mpdf->SetDisplayMode('fullpage');

            $mpdf->Output(\Storage::disk(self::PDF_DISK)->path($this->getUniquePdfFileName()), Destination::FILE);
        } catch (\Mpdf\MpdfException $e) {
            \Log::debug($e->getMessage());
        }
    }

    public function getPdfPath()
    {
        /*$fileName = $this->getUniquePdfFileName();
        if (!\Storage::disk(self::PDF_DISK)->exists($fileName)) {*/
            $this->toPdf();
        // }

        return \Storage::disk(self::PDF_DISK)->path($this->getUniquePdfFileName());
    }

    public function getUniquePdfFileName()
    {
        return $this->report->uniqueIdentifier() . '.pdf';
    }
}
