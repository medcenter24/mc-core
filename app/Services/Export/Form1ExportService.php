<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Services\Export;


use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use Maatwebsite\Excel\Facades\Excel;

/**
 *
 * Class Form1ExportService
 * @package medcenter24\mcCore\App\Services\Export
 * @deprecated moved to the Exports/CasesExport
 */
class Form1ExportService
{
    public function excel(array $filters)
    {
        return Excel::create('CasesExportForm1', function(LaravelExcelWriter $excel) use ($filters) {
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
            $row[trans('content.patient_name')] = $accident->patient ? $accident->patient->name : __('content.undefined');
            $row[trans('content.assistant')] = $accident->assistant ? $accident->assistant->title : __('content.undefined');
            $row[trans('content.assistant_ref_num')] = $accident->assistant_ref_num;
            $row[trans('content.ref_num')] = $accident->ref_num;
            $row[trans('content.date')] = $accident->created_at->format(config('date.dateFormat'));
            $row[trans('content.time')] = $accident->created_at->format(config('date.timeFormat'));
            $city = $service->getCity($accident);
            $row[trans('content.city')] = $city ? $city->title : trans('content.undefined');

            // Doctor case
            if ($accident->caseable instanceof DoctorAccident) {
                $row[trans('content.doctor')] = $accident->caseable->doctor ? $accident->caseable->doctor->name : trans('content.undefined');
            }

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
