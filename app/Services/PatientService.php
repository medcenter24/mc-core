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

namespace medcenter24\mcCore\App\Services;

use medcenter24\mcCore\App\Patient;

class PatientService extends AbstractModelService
{

    public const FIELD_ID = 'id';
    public const FIELD_NAME = 'name';
    public const FIELD_ADDRESS = 'address';
    public const FIELD_PHONES = 'phones';
    public const FIELD_BIRTHDAY = 'birthday';
    public const FIELD_COMMENT = 'comment';

    public const FILLABLE = [
        self::FIELD_NAME,
        self::FIELD_ADDRESS,
        self::FIELD_PHONES,
        self::FIELD_BIRTHDAY,
        self::FIELD_COMMENT,
    ];

    public const UPDATABLE = [
        self::FIELD_NAME,
        self::FIELD_ADDRESS,
        self::FIELD_PHONES,
        self::FIELD_BIRTHDAY,
        self::FIELD_COMMENT,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_NAME,
        self::FIELD_ADDRESS,
        self::FIELD_PHONES,
        self::FIELD_BIRTHDAY,
        self::FIELD_COMMENT,
    ];

    public function getClassName(): string
    {
        return Patient::class;
    }

    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_NAME => '',
            self::FIELD_ADDRESS => '',
            self::FIELD_PHONES => '',
            self::FIELD_COMMENT => '',
            self::FIELD_BIRTHDAY => null,
        ];
    }

    protected function getUpdatableFields(): array
    {
        return self::UPDATABLE;
    }
}
