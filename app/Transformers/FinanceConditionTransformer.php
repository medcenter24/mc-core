<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\FinanceCondition;
use League\Fractal\TransformerAbstract;

class FinanceConditionTransformer extends TransformerAbstract
{
    public function transform(FinanceCondition $financeCondition)
    {
        return [
            'id' => $financeCondition->id,
            'title' => $financeCondition->title,
            'priceAmount' => [], // $financeCondition->price,
            'assistants' => [], // $financeCondition->assistants->get('id'),
            'cities' => [], // $financeCondition->cities->get('id'),
            'doctors' => [], // $financeCondition->doctors->get('id'),
            'services' => [], // $financeCondition->services->get('id'),
            'datePeriods' => [], // $financeCondition->datePeriods()->get('id'),
        ];
    }
}
