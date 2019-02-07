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
        $data = $obj->collection;
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
                'fixed' => $item->get('fixed'),
            ];

            $iterator->next();
        }

        return $result;
    }
}
