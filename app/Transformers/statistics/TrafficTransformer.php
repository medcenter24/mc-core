<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers\statistics;


use League\Fractal\TransformerAbstract;

class TrafficTransformer extends TransformerAbstract
{
    public function transform($statistic) {
        return [
            'id' => $statistic->id,
            'name' => $statistic->name,
            'casesCount' => $statistic->cases_count,
        ];
    }
}
