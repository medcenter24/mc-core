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

namespace App\Transformers;


use App\Accident;
use App\DoctorAccident;
use App\HospitalAccident;
use League\Fractal\TransformerAbstract;

class CaseExportTransformer extends TransformerAbstract
{
    /**
     * @param Accident $accident
     * @return array
     */
    public function transform (Accident $accident): array
    {
        $row[] = $accident->patient ? $accident->patient->name : __('content.undefined');
        $row[] = $accident->assistant ? $accident->assistant->title : __('content.undefined');
        $row[] = $accident->assistant_ref_num;
        $row[] = $accident->ref_num;
        $row[] = $accident->created_at->format(config('date.dateFormat'));
        $row[] = $accident->created_at->format(config('date.timeFormat'));

        $city = $accident->getAttribute('city');
        $row[] = $city ? $city->title : trans('content.undefined');

        if ($accident->caseable instanceof DoctorAccident) {
            // caseable_type
            $row[] = trans('content.doctor');
            // caseable title
            $row[] = $accident->caseable->doctor ? $accident->caseable->doctor->name : trans('content.not_set');
        } elseif ($accident->caseable instanceof HospitalAccident) {
            $row[] = trans('content.hospital');
            $row[] = $accident->caseable->hospital ? $accident->caseable->hospital->title : trans('content.not_set');
        } else {
            $row[] = trans('content.not_set');
            $row[] = trans('content.not_set');
        }

        // caseable payment
        $row[] = $accident->paymentToCaseable ? $accident->paymentToCaseable->val : trans('content.not_set');

        // statuses
        // 0 - doesn't sent by doctor
        // 1 - sent by doctor
        // 2 - document sent to the assistant
        $row[] = trans('content.not_implemented');
        $row[] = trans('content.not_implemented');
        $row[] = trans('content.not_implemented');
        $row[] = trans('content.not_implemented');
        $row[] = trans('content.not_implemented');
        $row[] = trans('content.not_implemented');

        return $row;
    }
}
