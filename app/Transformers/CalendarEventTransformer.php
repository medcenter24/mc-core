<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Accident;
use League\Fractal\TransformerAbstract;

class CalendarEventTransformer extends TransformerAbstract
{
    public function transform(Accident $accident) {
        return [
            'id' => $accident->id,
            'title' => $accident->ref_num,
            'start' => $accident->created_at->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat')),
            'end' => $accident->visited_at ? $accident->visited_at->format(config('date.systemFormat')) : '',
            'status' => $accident->accidentStatus ? $accident->accidentStatus->title : '',
        ];
    }
}
