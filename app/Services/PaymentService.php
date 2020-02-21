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

namespace medcenter24\mcCore\App\Services;

use medcenter24\mcCore\App\Payment;

class PaymentService extends AbstractModelService
{
    public const FIELD_CREATED_BY = 'created_by';
    public const FIELD_VALUE = 'value';
    public const FIELD_CURRENCY_ID = 'currency_id';
    public const FIELD_FIXED = 'fixed';
    public const FIELD_DESCRIPTION = 'description';

    public const FILLABLE = [
        self::FIELD_CREATED_BY,
        self::FIELD_VALUE,
        self::FIELD_CURRENCY_ID,
        self::FIELD_FIXED,
        self::FIELD_DESCRIPTION,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_CREATED_BY,
        self::FIELD_VALUE,
        self::FIELD_CURRENCY_ID,
        self::FIELD_FIXED,
        self::FIELD_DESCRIPTION,
    ];

    public const UPDATABLE = [
        self::FIELD_CREATED_BY,
        self::FIELD_VALUE,
        self::FIELD_CURRENCY_ID,
        self::FIELD_FIXED,
        self::FIELD_DESCRIPTION,
    ];

    protected function getClassName(): string
    {
        return Payment::class;
    }

    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_VALUE=> 0,
            self::FIELD_CURRENCY_ID => 0,
            self::FIELD_FIXED => 1,
            self::FIELD_DESCRIPTION => '',
        ];
    }
}
