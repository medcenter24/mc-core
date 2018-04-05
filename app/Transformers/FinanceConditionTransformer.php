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
        return $financeCondition->toArray();
    }
}
