<?php
/**
 * Copyright (c) 2019.
 *
 * Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Payment;
use League\Fractal\TransformerAbstract;

class PaymentTransformer extends TransformerAbstract
{
    public function transform(Payment $payment): array
    {
        return [
            'id' => $payment->getAttribute('id'),
            'createdBy' => $payment->getAttribute('created_by'),
            'value' => $payment->getAttribute('value'),
            'currency_id' => $payment->getAttribute('currency_id'),
            'fixed' => (int)$payment->getAttribute('fixed') ? true : false,
            'description' => $payment->getAttribute('description'),
        ];
    }
}
