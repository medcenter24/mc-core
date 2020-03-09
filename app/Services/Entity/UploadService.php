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

use medcenter24\mcCore\App\Entity\Upload;

class UploadService extends AbstractModelService
{

    public const FIELD_PATH = 'path';
    public const FIELD_FILE_NAME = 'file_name';
    public const FIELD_STORAGE = 'storage';

    public const FILLABLE = [
        self::FIELD_PATH,
        self::FIELD_FILE_NAME,
        self::FIELD_STORAGE,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
    ];

    /**
     * @inheritDoc
     */
    protected function getClassName(): string
    {
        return Upload::class;
    }

    /**
     * @inheritDoc
     */
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_PATH => '',
            self::FIELD_FILE_NAME => '',
            self::FIELD_STORAGE => '',
        ];
    }
}
