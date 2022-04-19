<?php
/*
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
 * Copyright (c) 2022 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace medcenter24\mcCore\App\Services\Entity;

use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\App\Entity\SmartSearch;

class SmartSearchService extends AbstractModelService
{
    public const FIELD_ID = 'id';
    public const FIELD_TITLE = 'title';
    public const FIELD_TYPE = 'type';
    public const FIELD_BODY = 'body';
    public const FIELD_CREATED_AT = 'created_at';

    /**
     * That can be modified
     */
    public const FILLABLE = [
        self::FIELD_TITLE,
        self::FIELD_TYPE,
        self::FIELD_BODY,
    ];

    public const UPDATABLE = [
        self::FIELD_TITLE,
        self::FIELD_TYPE,
        self::FIELD_BODY,
    ];

    /**
     * That can be viewed
     */
    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_TITLE,
        self::FIELD_TYPE,
        self::FIELD_BODY,
    ];

    protected function getClassName(): string
    {
        return SmartSearch::class;
    }

    #[ArrayShape([
        self::FIELD_TITLE => "string",
        self::FIELD_BODY => "string"
    ])] protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_TITLE => '',
            self::FIELD_BODY  => '{}',
        ];
    }
}
