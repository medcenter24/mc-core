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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Transformers;

use medcenter24\mcCore\App\Services\Entity\AccidentService;

class AccidentTransformer extends AbstractTransformer
{
    protected function getMap(): array
    {
        return [
            AccidentService::FIELD_ID,
            'createdBy' => AccidentService::FIELD_CREATED_BY,
            'parentId' => AccidentService::FIELD_PARENT_ID,
            'patientId' => AccidentService::FIELD_PATIENT_ID,
            'accidentTypeId' => AccidentService::FIELD_ACCIDENT_TYPE_ID,
            'accidentStatusId' => AccidentService::FIELD_ACCIDENT_STATUS_ID,
            'assistantId' => AccidentService::FIELD_ASSISTANT_ID,
            'caseableId' => AccidentService::FIELD_CASEABLE_ID,
            'cityId' => AccidentService::FIELD_CITY_ID,
            'formReportId' => AccidentService::FIELD_FORM_REPORT_ID,
            'caseableType' => AccidentService::FIELD_CASEABLE_TYPE,
            'assistantPaymentId' => AccidentService::FIELD_ASSISTANT_PAYMENT_ID,
            'incomePaymentId' => AccidentService::FIELD_INCOME_PAYMENT_ID,
            'assistantInvoiceId' => AccidentService::FIELD_ASSISTANT_INVOICE_ID,
            'assistantGuaranteeId' => AccidentService::FIELD_ASSISTANT_GUARANTEE_ID,
            'caseablePaymentId' => AccidentService::FIELD_CASEABLE_PAYMENT_ID,
            'refNum' => AccidentService::FIELD_REF_NUM,
            'assistantRefNum' => AccidentService::FIELD_ASSISTANT_REF_NUM,
            'title' => AccidentService::FIELD_TITLE,
            'address' => AccidentService::FIELD_ADDRESS,
            'contacts' => AccidentService::FIELD_CONTACTS,
            'symptoms' => AccidentService::FIELD_SYMPTOMS,
            'createdAt' => AccidentService::FIELD_CREATED_AT,
            'closedAt' => AccidentService::FIELD_CLOSED_AT,
            'handlingTime' => AccidentService::FIELD_HANDLING_TIME,
            'updatedAt' => AccidentService::FIELD_UPDATED_AT,
            'deletedAt' => AccidentService::FIELD_DELETED_AT,
        ];
    }

    protected function getMappedTypes(): array
    {
        return [
            AccidentService::FIELD_ID => self::VAR_INT,
            AccidentService::FIELD_CREATED_BY => self::VAR_INT,
            AccidentService::FIELD_PARENT_ID => self::VAR_INT,
            AccidentService::FIELD_PATIENT_ID => self::VAR_INT,
            AccidentService::FIELD_ACCIDENT_TYPE_ID => self::VAR_INT,
            AccidentService::FIELD_ACCIDENT_STATUS_ID => self::VAR_INT,
            AccidentService::FIELD_ASSISTANT_ID => self::VAR_INT,
            AccidentService::FIELD_CASEABLE_ID => self::VAR_INT,
            AccidentService::FIELD_CITY_ID => self::VAR_INT,
            AccidentService::FIELD_FORM_REPORT_ID => self::VAR_INT,
            AccidentService::FIELD_ASSISTANT_PAYMENT_ID => self::VAR_INT,
            AccidentService::FIELD_INCOME_PAYMENT_ID => self::VAR_INT,
            AccidentService::FIELD_ASSISTANT_INVOICE_ID => self::VAR_INT,
            AccidentService::FIELD_ASSISTANT_GUARANTEE_ID => self::VAR_INT,
            AccidentService::FIELD_CASEABLE_PAYMENT_ID => self::VAR_INT,
            AccidentService::FIELD_CREATED_AT => self::VAR_DATETIME,
            AccidentService::FIELD_HANDLING_TIME => self::VAR_DATETIME,
            AccidentService::FIELD_CLOSED_AT => self::VAR_DATETIME,
            AccidentService::FIELD_UPDATED_AT => self::VAR_DATETIME,
            AccidentService::FIELD_DELETED_AT => self::VAR_DATETIME,
        ];
    }
}
