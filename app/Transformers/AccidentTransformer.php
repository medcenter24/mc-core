<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Accident;
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
