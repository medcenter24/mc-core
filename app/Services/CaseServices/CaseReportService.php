<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\CaseServices;

use App\Accident;
use App\Models\CaseReport;

class CaseReportService
{
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
}
