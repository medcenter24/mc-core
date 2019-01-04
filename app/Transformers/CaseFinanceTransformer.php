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
     * @param Collection $data
     * @return array
     */
    public function transform (Collection $data) : array
    {
        $result = [];
        $iterator = $data->getIterator();
        while ($iterator->valid()) {
            /** @var Collection $item */
            $item = $iterator->current();

            $result[] = [
                'type' => $item->get('type'),
                'loading' => false,
                'value' => $item->get('value'),
                'currency' => (new FinanceCurrencyTransformer())->transform($item->get('currency')),
                'formula' => $item->get('formula'),
                'financePayment' => (new PaymentTransformer())->transform($item->get('payment')),
            ];

            $iterator->next();
        }

        return $result;
    }
}
