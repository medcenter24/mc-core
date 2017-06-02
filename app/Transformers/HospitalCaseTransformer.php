<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use League\Fractal\TransformerAbstract;

// not implemented
class HospitalCaseTransformer extends TransformerAbstract
{
    public function transform(HospitalAccident $doctorAccident)
    {
        return [

        ];
    }
}
