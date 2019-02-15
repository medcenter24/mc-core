<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\FinanceCurrency;
use League\Fractal\TransformerAbstract;

class FinanceCurrencyTransformer extends TransformerAbstract
{
    public function transform(FinanceCurrency $financeCurrency): array
    {
        return [
            'id' => $financeCurrency->id,
            'title' => $financeCurrency->title,
            'code' => $financeCurrency->code,
            'ico' => $financeCurrency->ico,
        ];
    }
}
