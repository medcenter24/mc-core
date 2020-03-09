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

use medcenter24\mcCore\App\Entity\FormReport;

class FormReportService extends AbstractModelService
{
    public const FIELD_FORM_ID = 'form_id';
    public const FIELD_VALUES = 'values';

    public const FILLABLE = [
        self::FIELD_FORM_ID,
        self::FIELD_VALUES,
    ];

    public const UPDATABLE = [
        self::FIELD_VALUES,
    ];

    public const VISIBLE = [
        self::FIELD_FORM_ID,
        self::FIELD_VALUES,
    ];

    /**
     * @inheritDoc
     */
    protected function getClassName(): string
    {
        return FormReport::class;
    }

    /**
     * @inheritDoc
     */
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_FORM_ID => 0,
            self::FIELD_VALUES => '',
        ];
    }
}
