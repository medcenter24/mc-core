<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Services\Entity;

use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Entity\Invoice;

class HospitalAccidentService extends AbstractModelService
{

    public const FIELD_HOSPITAL_ID = 'hospital_id';
    public const FIELD_HOSPITAL_GUARANTEE_ID = 'hospital_guarantee_id';
    public const FIELD_HOSPITAL_INVOICE_ID = 'hospital_invoice_id';

    public const FILLABLE = [
        self::FIELD_HOSPITAL_ID,
        self::FIELD_HOSPITAL_GUARANTEE_ID,
        self::FIELD_HOSPITAL_INVOICE_ID,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_HOSPITAL_ID,
        self::FIELD_HOSPITAL_GUARANTEE_ID,
        self::FIELD_HOSPITAL_INVOICE_ID,
    ];

    public const UPDATABLE = [
        self::FIELD_HOSPITAL_ID,
        self::FIELD_HOSPITAL_GUARANTEE_ID,
        self::FIELD_HOSPITAL_INVOICE_ID,
    ];

    protected function getClassName(): string
    {
        return HospitalAccident::class;
    }

    protected function getFillableFieldDefaults(): array
    {
        return [];
    }

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