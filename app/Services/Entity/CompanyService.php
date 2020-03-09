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

use medcenter24\mcCore\App\Entity\Company;

class CompanyService extends AbstractModelService
{
    public const FIELD_TITLE = 'title';
    /**
     * Default for the company if exists
     */
    public const FIELD_HOSPITAL_ACCIDENT_FORM_ID = 'hospital_accident_form_id';
    public const FIELD_DOCTOR_ACCIDENT_FORM_ID = 'doctor_accident_form_id';

    public const FILLABLE = [
        self::FIELD_TITLE,
        self::FIELD_HOSPITAL_ACCIDENT_FORM_ID,
        self::FIELD_DOCTOR_ACCIDENT_FORM_ID,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_TITLE,
    ];

    public const UPDATABLE = [
        self::FIELD_TITLE,
        self::FIELD_HOSPITAL_ACCIDENT_FORM_ID,
        self::FIELD_DOCTOR_ACCIDENT_FORM_ID,
    ];

    /**
     * @inheritDoc
     */
    protected function getClassName(): string
    {
        return Company::class;
    }

    /**
     * @inheritDoc
     */
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_TITLE => '',
            self::FIELD_HOSPITAL_ACCIDENT_FORM_ID => 0,
            self::FIELD_DOCTOR_ACCIDENT_FORM_ID => 0,
        ];
    }
}
