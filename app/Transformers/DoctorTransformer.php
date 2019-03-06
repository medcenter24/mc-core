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
            'refKey' => $doctor->ref_key,
            'userId' => $doctor->user_id,
            'medicalBoardNumber' => $doctor->medical_board_num,
        ];
    }
}
