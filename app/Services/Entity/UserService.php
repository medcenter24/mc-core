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
use medcenter24\mcCore\App\Entity\User;

class UserService extends AbstractModelService
{

    public const THUMB_200 = 'thumb_200';
    public const THUMB_45 = 'thumb_45';

    public const FIELD_ID = 'id';
    public const FIELD_NAME = 'name';
    public const FIELD_EMAIL = 'email';
    public const FIELD_PASSWORD = 'password';
    public const FIELD_PHONE = 'phone';
    public const FIELD_LANG = 'lang';
    public const FIELD_TIMEZONE = 'timezone';
    public const FIELD_REMEMBER_TOKEN = 'remember_token';

    public const FILLABLE = [
        self::FIELD_NAME,
        self::FIELD_EMAIL,
        self::FIELD_PASSWORD,
        self::FIELD_PHONE,
        self::FIELD_LANG,
        self::FIELD_TIMEZONE,
    ];

    public const UPDATABLE = [
        self::FIELD_NAME,
        self::FIELD_EMAIL,
        self::FIELD_PASSWORD,
        self::FIELD_PHONE,
        self::FIELD_LANG,
        self::FIELD_TIMEZONE,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_NAME,
        self::FIELD_EMAIL,
        self::FIELD_PASSWORD,
        self::FIELD_PHONE,
        self::FIELD_LANG,
        self::FIELD_TIMEZONE,
    ];

    public const HIDDEN = [
        self::FIELD_PASSWORD,
        self::FIELD_REMEMBER_TOKEN,
    ];

    // We can define rule in the configuration maybe
    /**
     * @param string $email
     * @return bool
     */
    public function isValidEmail(string $email): bool
    {
        return !empty($email);
    }

    /**
     * @param string $password
     * @return bool
     */
    public function isValidPassword(string $password): bool
    {
        return !empty($password);
    }

    /**
     * Active user timezone or default timezone
     * @return string
     */
    public function getTimezone(): string
    {
        $timezone = 'UTC';
        if (auth()->check() && auth()->user()->timezone) {
            $timezone = auth()->user()->timezone;
        }
        return $timezone;
    }

    /**
     * Name of the Model(ex: City::class)
     * @return string
    */
    protected function getClassName(): string
    {
        return User::class;
    }

    /**
     * Initialize defaults to avoid database exceptions
     * (different storage have different rules, so it is correct to set defaults instead of nothing)
     * @return array
     */
    #[ArrayShape([self::FIELD_NAME     => "string",
                  self::FIELD_EMAIL    => "string",
                  self::FIELD_PHONE    => "string",
                  self::FIELD_LANG     => "string",
                  self::FIELD_TIMEZONE => "string"
    ])] protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_NAME => '',
            self::FIELD_EMAIL => '',
            self::FIELD_PHONE => '',
            self::FIELD_LANG => 'en',
            self::FIELD_TIMEZONE => 'UTC'
        ];
    }
}
