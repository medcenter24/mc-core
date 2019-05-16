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

namespace medcenter24\mcCore\App\Transformers;


use medcenter24\mcCore\App\Accident;
use League\Fractal\TransformerAbstract;

class AccidentTransformer extends TransformerAbstract
{
    /**
     * @param Accident $accident
     * @return array
     */
    public function transform (Accident $accident)
    {
        return [
            'id' => $accident->id,
            'createdBy' =>$accident->created_by,
            'parentId' => $accident->parent_id,
            'patientId' => $accident->patient_id,
            'accidentTypeId' => $accident->accident_type_id,
            'accidentStatusId' => $accident->accident_status_id,
            'assistantId' => $accident->assistant_id,
            'caseableId' => $accident->caseable_id,
            'cityId' => $accident->city_id,
            'formReportId' => $accident->form_report_id,
            'caseableType' => $accident->caseable_type,
            'assistantPaymentId' => $accident->assistant_payment_id,
            'incomePaymentId' => $accident->income_payment_id,
            'assistantInvoiceId' => $accident->assistant_invoice_id,
            'assistantGuaranteeId' => $accident->assistant_guarantee_id,
            'caseablePaymentId' => $accident->caseable_payment_id,
            'refNum' => $accident->ref_num,
            'assistantRefNum' => $accident->assistant_ref_num,
            'title' => $accident->title,
            'address' => $accident->address,
            'contacts' => $accident->contacts,
            'symptoms' => $accident->symptoms,
            // system format needed by the director case editor
            'createdAt' => $accident->created_at->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat')),
            'updatedAt' => $accident->updated_at ? $accident->updated_at->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat')) : null,
            'deletedAt' => $accident->deleted_at ? $accident->deleted_at->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat')) : null,
            'closedAt' => $accident->closed_at,
            'handlingTime' => $accident->handling_time ? $accident->handling_time->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat')) : null,
        ];
    }
}
