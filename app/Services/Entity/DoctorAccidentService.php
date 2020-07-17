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

use medcenter24\mcCore\App\Entity\DoctorAccident;

class DoctorAccidentService extends AbstractModelService
{

    public const FIELD_DOCTOR_ID = 'doctor_id';
    public const FIELD_RECOMMENDATION = 'recommendation';
    public const FIELD_INVESTIGATION = 'investigation';
    public const FIELD_VISIT_TIME = 'visit_time';

    public const DATE_FIELDS = [
        self::FIELD_CREATED_AT,
        self::FIELD_DELETED_AT,
        self::FIELD_UPDATED_AT,
        self::FIELD_VISIT_TIME,
    ];

    public const FILLABLE = [
        self::FIELD_DOCTOR_ID,
        self::FIELD_RECOMMENDATION,
        self::FIELD_INVESTIGATION,
        self::FIELD_VISIT_TIME,
    ];

    public const UPDATABLE = [
        self::FIELD_DOCTOR_ID,
        self::FIELD_RECOMMENDATION,
        self::FIELD_INVESTIGATION,
        self::FIELD_VISIT_TIME,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_DOCTOR_ID,
        self::FIELD_RECOMMENDATION,
        self::FIELD_INVESTIGATION,
        self::FIELD_VISIT_TIME,
    ];

    public function getClassName(): string
    {
        return DoctorAccident::class;
    }

    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_DOCTOR_ID => 0,
            self::FIELD_RECOMMENDATION => '',
            self::FIELD_INVESTIGATION => '',
        ];
    }
}
