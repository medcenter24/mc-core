<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Scenario;
use League\Fractal\TransformerAbstract;

class ScenarioTransformer extends TransformerAbstract
{
    public function transform (Scenario $scenario)
    {
        return [
            'id' => $scenario->id,
            'tag' => $scenario->tag,
            'order' => $scenario->order,
            'mode' => $scenario->mode,
            'accident_status_id' => $scenario->accident_status_id,
            'status' => $scenario->status ?: '',
            'title' => $scenario->accidentStatus->title,
        ];
    }
}
