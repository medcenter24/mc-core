<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\DatePeriod;
use League\Fractal\TransformerAbstract;

class DatePeriodTransformer extends TransformerAbstract
{
    public function transform(DatePeriod $datePeriod)
    {
        return [
            'id' => $datePeriod->id,
            'title' => $datePeriod->title,
            'from' => $datePeriod->from,
            'to' => $datePeriod->to,
        ];
    }
}
