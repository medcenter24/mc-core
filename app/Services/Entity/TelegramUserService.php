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
use medcenter24\mcCore\App\Entity\TelegramUser;

class TelegramUserService extends AbstractModelService
{

    public const FIELD_TELEGRAM_ID = 'telegram_id';
    public const FIELD_USER_ID = 'user_id';
    public const FIELD_USERNAME = 'username';
    public const FIELD_LAST_VISIT = 'last_visit';
    public const FIELD_FIRST_NAME = 'first_name';
    public const FIELD_LAST_NAME = 'last_name';

    public const FILLABLE = [
        self::FIELD_TELEGRAM_ID,
        self::FIELD_USER_ID,
        self::FIELD_USERNAME,
        self::FIELD_LAST_VISIT,
        self::FIELD_FIRST_NAME,
        self::FIELD_LAST_NAME,
    ];

    public const UPDATABLE = [
        self::FIELD_TELEGRAM_ID,
        self::FIELD_USER_ID,
        self::FIELD_USERNAME,
        self::FIELD_LAST_VISIT,
        self::FIELD_FIRST_NAME,
        self::FIELD_LAST_NAME,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_TELEGRAM_ID,
        self::FIELD_USER_ID,
        self::FIELD_USERNAME,
        self::FIELD_LAST_VISIT,
        self::FIELD_FIRST_NAME,
        self::FIELD_LAST_NAME,
    ];

    /**
     * @inheritDoc
     */
    protected function getClassName(): string
    {
        return TelegramUser::class;
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        self::FIELD_TELEGRAM_ID => "int",
        self::FIELD_USER_ID     => "int",
        self::FIELD_USERNAME    => "string",
        self::FIELD_LAST_VISIT  => "string",
        self::FIELD_FIRST_NAME  => "string",
        self::FIELD_LAST_NAME   => "string"
    ])] protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_TELEGRAM_ID => 0,
            self::FIELD_USER_ID => 0,
            self::FIELD_USERNAME => '',
            self::FIELD_LAST_VISIT => '',
            self::FIELD_FIRST_NAME => '',
            self::FIELD_LAST_NAME => '',
        ];
    }
}
