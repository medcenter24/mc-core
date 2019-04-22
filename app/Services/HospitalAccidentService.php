<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Services;


use medcenter24\mcCore\App\HospitalAccident;
use medcenter24\mcCore\App\Invoice;

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
