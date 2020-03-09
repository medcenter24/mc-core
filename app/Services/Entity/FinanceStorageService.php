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

use medcenter24\mcCore\App\Entity\FinanceStorage;

class FinanceStorageService extends AbstractModelService
{

    public const FIELD_FINANCE_CONDITION_ID = 'finance_condition_id';
    public const FIELD_MODEL = 'model';
    public const FIELD_MODEL_ID = 'model_id';

    public const FILLABLE = [
        self::FIELD_FINANCE_CONDITION_ID,
        self::FIELD_MODEL,
        self::FIELD_MODEL_ID,
    ];

    public const UPDATABLE = [
        self::FIELD_FINANCE_CONDITION_ID,
        self::FIELD_MODEL,
        self::FIELD_MODEL_ID,
    ];

    public const VISIBLE = [
        self::FIELD_FINANCE_CONDITION_ID,
        self::FIELD_MODEL,
        self::FIELD_MODEL_ID,
    ];

    /**
     * @inheritDoc
     */
    protected function getClassName(): string
    {
        return FinanceStorage::class;
    }

    /**
     * @inheritDoc
     */
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_FINANCE_CONDITION_ID => 0,
            self::FIELD_MODEL => '',
            self::FIELD_MODEL_ID => 0,
        ];
    }
}
