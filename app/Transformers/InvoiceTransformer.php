<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Invoice;
use League\Fractal\TransformerAbstract;

class InvoiceTransformer extends TransformerAbstract
{
    /**
     * @param Invoice $invoice
     * @return array
     */
    public function transform (Invoice $invoice)
    {
        return [
            'id'   => $invoice->id,
            'title' => $invoice->title,
            'type' => $invoice->type,
            'price' => $invoice->payment ? $invoice->payment->value : 0,
            'status' => $invoice->status,
        ];
    }
}
