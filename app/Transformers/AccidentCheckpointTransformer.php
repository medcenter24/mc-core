<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\AccidentCheckpoint;
use League\Fractal\TransformerAbstract;

class AccidentCheckpointTransformer extends TransformerAbstract
{
    public function transform(AccidentCheckpoint $accidentCheckpoint)
    {
        return [
            'id' => $accidentCheckpoint->id,
            'title' => $accidentCheckpoint->title,
            'description' => $accidentCheckpoint->description
        ];
    }
}
