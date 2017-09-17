<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Accident;
use App\Patient;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PatientTransformer extends TransformerAbstract
{
    public function transform(Patient $patient)
    {
        return [
            'id' => $patient->id,
            'name' => $patient->name,
            'address' => $patient->address,
            'phones' => $patient->phones,
            'birthday' => $patient->birthday,
            'comment' => $patient->comment
        ];
    }
}
