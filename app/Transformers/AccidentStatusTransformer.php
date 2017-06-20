<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\AccidentStatus;
use League\Fractal\TransformerAbstract;

class AccidentStatusTransformer extends TransformerAbstract
{
    public function transform(AccidentStatus $accidentStatus)
    {
        return [
            'id' => $accidentStatus->id,
            'title' => $accidentStatus->title,
            'type' => $accidentStatus->type
        ];
    }
}
