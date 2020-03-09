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

use medcenter24\mcCore\App\Entity\DatePeriod;

class DatePeriodInterpretationService extends AbstractModelService
{

    public const FIELD_DATE_PERIOD_ID = 'date_period_id';
    public const FIELD_DAY_OF_WEEK = 'day_of_week';
    public const FIELD_FROM = 'from';
    public const FIELD_TO = 'to';

    public const FILLABLE = [
        self::FIELD_DATE_PERIOD_ID,
        self::FIELD_DAY_OF_WEEK,
        self::FIELD_FROM,
        self::FIELD_TO,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_DATE_PERIOD_ID,
        self::FIELD_DAY_OF_WEEK,
        self::FIELD_FROM,
        self::FIELD_TO,
    ];

    public const UPDATABLE = [
        self::FIELD_DATE_PERIOD_ID,
        self::FIELD_DAY_OF_WEEK,
        self::FIELD_FROM,
        self::FIELD_TO,
    ];

    /**
     * @inheritDoc
     */
    protected function getClassName(): string
    {
        return DatePeriod::class;
    }

    /**
     * @inheritDoc
     */
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_DATE_PERIOD_ID => 0,
            self::FIELD_DAY_OF_WEEK => '',
            self::FIELD_FROM => '',
            self::FIELD_TO => '',
        ];
    }
}
