<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\AccidentType;
use League\Fractal\TransformerAbstract;

class AccidentTypeTransformer extends TransformerAbstract
{
    public function transform(AccidentType $accidentType)
    {
        return [
            'id' => $accidentType->id,
            'title' => $accidentType->title,
            'description' => $accidentType->description
        ];
    }
}
