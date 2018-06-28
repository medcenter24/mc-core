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
            'accident_id' => $hospitalAccident->accident->id,
            'hospital_id' => $hospitalAccident->hospital_id,
            'hospital_guarantee_id' => $hospitalAccident->hospital_guarantee_id,
            'hospital_invoice_id' => $hospitalAccident->hospital_invoice_id,
            'assistant_invoice_id' => $hospitalAccident->assistant_invoice_id,
            'assistant_guarantee_id' => $hospitalAccident->assistant_guarantee_id,
        ];
    }
}
