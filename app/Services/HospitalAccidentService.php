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
        $this->saveInvoice('hospital', $hospitalAccident, $data);
    }

    public function saveAssistantInvoice(HospitalAccident $hospitalAccident, array $data = null)
    {
        $this->saveInvoice('assistant', $hospitalAccident, $data);
    }

    private function saveInvoice($name = 'hospital', HospitalAccident $hospitalAccident, array $data = null)
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
                // save morph doc
            }
            if (isset($data['price'])) {
                $invoice->price = $data['price'];
            }
        }
    }
}
