<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Export;


use App\Accident;
use App\Services\AccidentService;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class Form1ExportService
{
    public function excel(array $filters)
    {
        return \Excel::create('CasesExportForm1', function(LaravelExcelWriter $excel) use ($filters) {
            // Set the title
            $excel->setTitle('Export Form 1');
            // Chain the setters
            $excel->setCreator(trans('content.project_name'))
                ->setCompany(env('CUSTOMER_NAME', 'customer name'));
            // Call them separately
            $excel->setDescription(trans('content.export_form1_desc'));

            // Our first sheet
            $excel->sheet(trans('content.cases'), function($sheet) use ($filters) {
                $sheet->fromArray($this->data($filters));
            });
        });
    }

    /**
     * Load data with cases for the current export
     * @param array $filters
     * @return array
     */
    private function data(array $filters)
    {
        $rows = [];
        $service = new AccidentService();
        $accidents = $service->getCasesQuery($filters);
        /** @var Accident $accident */
        foreach ($accidents->get() as $i => $accident) {
            $row = [];
            $row[trans('content.npp')] = $i+1;
            $row[trans('content.patient_name')] = $accident->patient->name;
            $row[trans('content.assistant')] = $accident->assistant->title;
            $row[trans('content.assistant_ref_num')] = $accident->assistant_ref_num;
            $row[trans('content.ref_num')] = $accident->ref_num;
            $row[trans('content.date')] = $accident->created_at->format(config('date.dateFormat'));
            $row[trans('content.time')] = $accident->created_at->format(config('date.timeFormat'));
            $row[trans('content.city')] = $accident->city_id ? $accident->city->title : trans('content.undefined');

            // Doctor case
            $row[trans('content.doctor')] = $accident->caseable_id ? $accident->caseable->name : trans('content.undefined');
            $row[trans('content.doctor_fee')] = trans('content.not_implemented');
            $row[trans('content.final_doctor_fee')] = trans('content.not_implemented');

            // statuses
            // 0 - doesn't sent by doctor
            // 1 - sent by doctor
            // 2 - document sent to the assistant
            $row[trans('content.report')] = trans('content.not_implemented');
            $row[trans('content.policy')] = trans('content.not_implemented');
            $row[trans('content.passport')] = trans('content.not_implemented');
            $row[trans('content.passport_checks')] = trans('content.not_implemented');
            $row[trans('content.payment_guaranty')] = trans('content.not_implemented');
            $row[trans('content.paid')] = trans('content.not_implemented');

            $rows[] = $row;
        }
        return $rows;
    }
}
