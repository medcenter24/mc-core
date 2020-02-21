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

namespace medcenter24\mcCore\App\Services;

use medcenter24\mcCore\App\Hospital;

class HospitalService extends AbstractModelService
{

    public const FIELD_TITLE = 'title';
    public const FIELD_REF_KEY = 'ref_key';
    public const FIELD_ADDRESS = 'address';
    public const FIELD_PHONES = 'phones';
    public const FIELD_DESCRIPTION = 'description';

    public const FILLABLE = [
        self::FIELD_TITLE,
        self::FIELD_REF_KEY,
        self::FIELD_ADDRESS,
        self::FIELD_PHONES,
        self::FIELD_DESCRIPTION,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_TITLE,
        self::FIELD_REF_KEY,
        self::FIELD_ADDRESS,
        self::FIELD_PHONES,
        self::FIELD_DESCRIPTION,
    ];

    public const UPDATABLE = [
        self::FIELD_TITLE,
        self::FIELD_REF_KEY,
        self::FIELD_ADDRESS,
        self::FIELD_PHONES,
        self::FIELD_DESCRIPTION,
    ];

    public function getClassName(): string
    {
        return Hospital::class;
    }

    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_TITLE => '',
            self::FIELD_REF_KEY => '',
            self::FIELD_ADDRESS => '',
            self::FIELD_PHONES => '',
            self::FIELD_DESCRIPTION => '',
        ];
    }

    protected function getUpdatableFields(): array
    {
        return self::UPDATABLE;
    }
}
