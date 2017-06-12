<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\DoctorAccident;
use League\Fractal\TransformerAbstract;

class DoctorAccidentStatusTransformer extends TransformerAbstract
{
    public function transform(DoctorAccident $doctorAccident)
    {
        return [
            'id' => $doctorAccident->id,
            'status' => $doctorAccident->status
        ];
    }
}
