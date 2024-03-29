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

use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\App\Entity\Patient;

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

    public const DATE_FIELDS = [
        self::FIELD_CREATED_AT,
        self::FIELD_DELETED_AT,
        self::FIELD_UPDATED_AT,
        self::FIELD_BIRTHDAY,
    ];

    public function getClassName(): string
    {
        return Patient::class;
    }

    #[ArrayShape([
        self::FIELD_NAME => "string",
        self::FIELD_ADDRESS => "string",
        self::FIELD_PHONES => "string",
        self::FIELD_COMMENT => "string",
        self::FIELD_BIRTHDAY => "null"
    ])] protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_NAME => '',
            self::FIELD_ADDRESS => '',
            self::FIELD_PHONES => '',
            self::FIELD_COMMENT => '',
            self::FIELD_BIRTHDAY => null,
        ];
    }

    protected function prepareDataForUpdate(array $data = []): array
    {
        parent::prepareDataForUpdate($data);
        $data[self::FIELD_BIRTHDAY] = empty($data[self::FIELD_BIRTHDAY]) ? null : $data[self::FIELD_BIRTHDAY];
        return $data;
    }
}
