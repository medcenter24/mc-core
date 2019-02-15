<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\HospitalAccident;
use League\Fractal\TransformerAbstract;

class HospitalCaseTransformer extends TransformerAbstract
{
    public function transform(HospitalAccident $hospitalAccident)
    {
        return [
            'id' => $hospitalAccident->id,
            'accidentId' => $hospitalAccident->accident->id,
            'hospitalId' => $hospitalAccident->hospital_id,
            'hospitalGuaranteeId' => $hospitalAccident->hospital_guarantee_id,
            'hospitalInvoiceId' => $hospitalAccident->hospital_invoice_id,
            'assistantInvoiceId' => $hospitalAccident->assistant_invoice_id,
            'assistantGuaranteeId' => $hospitalAccident->assistant_guarantee_id,
        ];
    }
}
