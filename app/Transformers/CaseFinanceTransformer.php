<?php
/**
 * Copyright (c) 2019.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;

/**
 * Used for the output into the data table
 * Class CasesTransformer
 * @package App\Transformers
 */
class CaseFinanceTransformer extends TransformerAbstract
{
    /**
     * @param \stdClass $obj
     * @return array
     */
    public function transform (\stdClass $obj) : array
    {
        $result = [];
        $obj->collection->each(function (Collection $item) use (&$result) {
            $payment = $item->get('payment');
            $result[] = [
                'type' => $item->get('type'),
                'loading' => false,
                'payment' => $payment ? (new PaymentTransformer())->transform($payment) : false,
                'currency' => $item->get('currency') ? (new FinanceCurrencyTransformer())->transform($item->get('currency')) : null,
                'formula' => $item->get('formulaView'),
                'calculatedValue' => $item->get('calculatedValue'),
            ];
        });

        return $result;
    }
}
