<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\HospitalAccident;
use App\Invoice;

class HospitalAccidentService
{
    public function saveHospitalInvoice(HospitalAccident $hospitalAccident, array $data = null)
    {
        $this->saveInvoice($hospitalAccident, $data, 'hospital');
    }

    public function saveAssistantInvoice(HospitalAccident $hospitalAccident, array $data = null)
    {
        $this->saveInvoice($hospitalAccident, $data, 'assistant');
    }

    private function saveInvoice(HospitalAccident $hospitalAccident, array $data = null, $name = 'hospital')
    {
        $uri = $name . '_invoice_id';
        $morphName = $name . 'Invoice';
        if (!$hospitalAccident->$uri) {
            $invoice = Invoice::create();
            $hospitalAccident->$uri = $invoice->id;
        } else {
            $invoice = $hospitalAccident->$morphName;
        }

        if ($data && is_array($data)) {
            if (isset($data['documentId'])) {
                // save morph doc todo?
            }
            if (isset($data['price'])) {
                $invoice->price = $data['price'];
            }
        }
    }
}
