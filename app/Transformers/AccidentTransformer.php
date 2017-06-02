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
    public function transform(Accident $accident)
    {
        return [
            'id' => $accident->id,
            'created_by' => $accident->created_by,
            'parent_id' => $accident->parent_id,
            'patient_id' => $accident->patient_id,
            'accident_type_id' => $accident->accident_type_id,
            'accident_status_id' => $accident->accident_status_id,
            'assistant_id' => $accident->assistant_id,
            'caseable_id' => $accident->caseable_id,
            'city_id' => $accident->city_id,
            'form_report_id' => $accident->form_report_id,
            'discount_value' => 5.075,
            'discount_type_id' => 2,
            'caseable_type' => $accident->caseable_type,
            'ref_num' => $accident->ref_num,
            'title' => $accident->title,
            'address' => $accident->address,
            'contacts' => $accident->contacts,
            'symptoms' => $accident->symptoms,
            'created_at' => $accident->created_at->format(config('date.actionFormat')),
            'closed_at' => '',
        ];
    }
}
