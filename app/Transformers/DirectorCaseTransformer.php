<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Accident;
use App\Discount;
use League\Fractal\TransformerAbstract;

/**
 * Load aggregated Data for the directors preview
 *
 * Class CaseTransformer
 * @package App\Transformers
 */
class DirectorCaseTransformer extends TransformerAbstract
{
    /**
     * @param Accident $accident
     * @return array
     */
    public function transform (Accident $accident)
    {
        return [
            'accident' => (new AccidentTransformer())->transform($accident),
            'discountType' => (new DiscountTransformer())->transform($accident->discount ? $accident->discount : new Discount())
        ];
    }
}
