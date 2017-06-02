<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Discount;
use League\Fractal\TransformerAbstract;

class DiscountTransformer extends TransformerAbstract
{
    public function transform(Discount $discount)
    {
        return [
            'id' => $discount->id,
            'title' => $discount->title,
            'description' => $discount->description,
            'operation' => $discount->operation,
        ];
    }
}
