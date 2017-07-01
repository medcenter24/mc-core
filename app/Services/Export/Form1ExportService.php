<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Export;


use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class Form1ExportService
{
    public function excel(array $filters)
    {
        return \Excel::create('CasesExportForm1', function(LaravelExcelWriter $excel) {
            // Set the title
            $excel->setTitle('Export Form 1');
            // Chain the setters
            $excel->setCreator(trans('content.project_name'))
                ->setCompany(env('CUSTOMER_NAME', 'customer name'));
            // Call them separately
            $excel->setDescription(trans('content.export_form1_desc'));

            // Our first sheet
            $excel->sheet(trans('content.cases'), function($sheet) {
                $data = [['fieldName'=>'fieldContent']];
                $sheet->fromArray($data);
            });
        });
    }
}
