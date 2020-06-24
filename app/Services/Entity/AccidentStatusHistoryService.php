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

use medcenter24\mcCore\App\Entity\AccidentStatusHistory;

class AccidentStatusHistoryService extends AbstractModelService
{

    public const FIELD_USER_ID = 'user_id';
    public const FIELD_ACCIDENT_STATUS_ID = 'accident_status_id';
    public const FIELD_HISTORYABLE_ID = 'historyable_id';
    public const FIELD_HISTORYABLE_TYPE = 'historyable_type';
    public const FIELD_COMMENTARY = 'commentary';

    public const FILLABLE = [
        self::FIELD_USER_ID,
        self::FIELD_ACCIDENT_STATUS_ID,
        self::FIELD_HISTORYABLE_ID,
        self::FIELD_HISTORYABLE_TYPE,
        self::FIELD_COMMENTARY,
    ];

    public const UPDATABLE = [
        self::FIELD_USER_ID,
        self::FIELD_ACCIDENT_STATUS_ID,
        self::FIELD_HISTORYABLE_ID,
        self::FIELD_HISTORYABLE_TYPE,
        self::FIELD_COMMENTARY,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_USER_ID,
        self::FIELD_ACCIDENT_STATUS_ID,
        self::FIELD_HISTORYABLE_ID,
        self::FIELD_HISTORYABLE_TYPE,
        self::FIELD_COMMENTARY,
        self::FIELD_CREATED_AT,
        self::FIELD_UPDATED_AT,
        self::FIELD_DELETED_AT,
    ];

    /**
     * @inheritDoc
     */
    protected function getClassName(): string
    {
        return AccidentStatusHistory::class;
    }

    /**
     * @inheritDoc
     */
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_USER_ID => 0,
            self::FIELD_ACCIDENT_STATUS_ID => 0,
            self::FIELD_HISTORYABLE_ID => 0,
            self::FIELD_HISTORYABLE_TYPE => '',
            self::FIELD_COMMENTARY => '',
        ];
    }
}
