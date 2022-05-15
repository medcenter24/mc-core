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
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Services\Form;

class FormVariableService
{
    public const TYPE_ACCIDENT = 'accident';

    public const VAR_REG_EX = ':[a-zA-Z0-9\._]+';
    public const VAR_ACCIDENT_ASSISTANT_TITLE = ':accident.assistant.title';
    public const VAR_ACCIDENT_ASSISTANT_COMMENT = ':accident.assistant.comment';
    public const VAR_ACCIDENT_PATIENT_NAME = ':accident.patient.name';
    public const VAR_ACCIDENT_PATIENT_BIRTHDAY = ':accident.patient.birthday.date';
    public const VAR_ACCIDENT_ASSISTANT_REF_NUM = ':accident.assistant_ref_num';
    public const VAR_ACCIDENT_REF_NUM = ':accident.ref_num';
    public const VAR_ACCIDENT_SYMPTOMS = ':accident.symptoms';
    public const VAR_ACCIDENT_CASEABLE_INVESTIGATION = ':accident.caseable.investigation';
    public const VAR_ACCIDENT_CASEABLE_RECOMMENDATION = ':accident.caseable.recommendation';
    public const VAR_ACCIDENT_PARENT_ID = ':accident.parent_id';
    public const VAR_ACCIDENT_PARENT_REF_NUM = ':accident.parent.ref_num';
    public const VAR_ACCIDENT_CASEABLE_DOCTOR_NAME = ':accident.caseable.doctor.name';
    public const VAR_ACCIDENT_CASEABLE_DOCTOR_MEDICAL_BOARD_NUM = ':accident.caseable.doctor.medical_board_num';
    public const VAR_ACCIDENT_CASEABLE_HOSPITAL_TITLE = ':accident.caseable.hospital.title';
    public const VAR_ACCIDENT_CASEABLE_DIAGNOSTICS = ':accident.caseable.diagnostics';
    public const VAR_ACCIDENT_CASEABLE_SURVEYS = ':accident.caseable.surveys';
    public const VAR_ACCIDENT_CASEABLE_SERVICES = ':accident.caseable.services';
    public const VAR_ACCIDENT_CASEABLE_VISIT_TIME_TIME = ':accident.caseable.visit_time.time';
    public const VAR_ACCIDENT_CASEABLE_VISIT_TIME_DATE = ':accident.caseable.visit_time.date';
    public const VAR_ACCIDENT_CITY_REGION_COUNTRY_TITLE = ':accident.city.region.country.title';
    public const VAR_ACCIDENT_CITY_REGION_TITLE = ':accident.city.region.title';
    public const VAR_ACCIDENT_CITY_TITLE = ':accident.city.title';
    public const VAR_ACCIDENT_DOCUMENTS = ':accident.documents';

    public const VAR_ACCIDENT_INCOME_CURRENCY_ICO = ':accident.income.currency.ico';
    public const VAR_ACCIDENT_INCOME_CURRENCY_TITLE = ':accident.income.currency.title';
    public const VAR_ACCIDENT_INCOME_VALUE = ':accident.income.value';

    public const VAR_ACCIDENT_ASSISTANT_INVOICE_PRICE = ':accident.assistantInvoice.payment.value';

    public const INCOME_VARS = [
        self::VAR_ACCIDENT_INCOME_CURRENCY_ICO,
        self::VAR_ACCIDENT_INCOME_CURRENCY_TITLE,
        self::VAR_ACCIDENT_INCOME_VALUE,
    ];

    /**
     * Variables that should be loaded with services
     */
    public const PROGRAMMED_VARS = self::INCOME_VARS;

    public function getAccidentVariables(): array
    {
        return [
            self::VAR_ACCIDENT_ASSISTANT_TITLE,
            self::VAR_ACCIDENT_ASSISTANT_COMMENT,
            self::VAR_ACCIDENT_PATIENT_NAME,
            self::VAR_ACCIDENT_PATIENT_BIRTHDAY,
            self::VAR_ACCIDENT_ASSISTANT_REF_NUM,
            self::VAR_ACCIDENT_REF_NUM,
            self::VAR_ACCIDENT_SYMPTOMS,
            self::VAR_ACCIDENT_CASEABLE_INVESTIGATION,
            self::VAR_ACCIDENT_CASEABLE_RECOMMENDATION,
            self::VAR_ACCIDENT_PARENT_ID,
            self::VAR_ACCIDENT_PARENT_REF_NUM,
            self::VAR_ACCIDENT_CASEABLE_DOCTOR_NAME,
            self::VAR_ACCIDENT_CASEABLE_DOCTOR_MEDICAL_BOARD_NUM,
            self::VAR_ACCIDENT_CASEABLE_HOSPITAL_TITLE,
            self::VAR_ACCIDENT_INCOME_CURRENCY_ICO,
            self::VAR_ACCIDENT_CASEABLE_DIAGNOSTICS,
            self::VAR_ACCIDENT_CASEABLE_SURVEYS,
            self::VAR_ACCIDENT_CASEABLE_SERVICES,
            self::VAR_ACCIDENT_INCOME_CURRENCY_TITLE,
            self::VAR_ACCIDENT_INCOME_VALUE,
            self::VAR_ACCIDENT_CASEABLE_VISIT_TIME_TIME,
            self::VAR_ACCIDENT_CASEABLE_VISIT_TIME_DATE,
            self::VAR_ACCIDENT_CITY_REGION_COUNTRY_TITLE,
            self::VAR_ACCIDENT_CITY_REGION_TITLE,
            self::VAR_ACCIDENT_CITY_TITLE,
            self::VAR_ACCIDENT_DOCUMENTS,
            self::VAR_ACCIDENT_ASSISTANT_INVOICE_PRICE,
        ];
    }
}
