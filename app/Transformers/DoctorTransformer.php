<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Doctor;
use League\Fractal\TransformerAbstract;

class DoctorTransformer extends TransformerAbstract
{
    public function transform(Doctor $doctor)
    {
        return [
            'id' => $doctor->id,
            'name' => $doctor->name,
            'description' => $doctor->description,
            'ref_key' => $doctor->ref_key,
            'user_id' => $doctor->user_id,
            'medical_board_num' => $doctor->medical_board_num,
        ];
    }
}
