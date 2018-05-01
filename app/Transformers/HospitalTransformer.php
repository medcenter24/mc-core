<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Hospital;
use League\Fractal\TransformerAbstract;

class HospitalTransformer extends TransformerAbstract
{
    public function transform(Hospital $hospital)
    {
        return [
            'id' => $hospital->id,
            'title' => $hospital->title,
            'description' => $hospital->description,
            'refKey' => $hospital->ref_key,
            'phones' => $hospital->phones,
            'address' => $hospital->address,
        ];
    }
}
