<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
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

declare(strict_types=1);

namespace medcenter24\mcCore\App\Transformers;

use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\App\Services\Entity\SmartSearchService;

class SmartSearchTransformer extends AbstractTransformer
{
    protected function getMap(): array
    {
        return [
            SmartSearchService::FIELD_ID,
            SmartSearchService::FIELD_TITLE,
            SmartSearchService::FIELD_TYPE,
            SmartSearchService::FIELD_BODY,
        ];
    }

    #[ArrayShape([
        SmartSearchService::FIELD_ID    => "string",
        SmartSearchService::FIELD_TITLE => "string",
        SmartSearchService::FIELD_TYPE  => "string",
        SmartSearchService::FIELD_BODY  => "string",
    ])]
    protected function getMappedTypes(): array
    {
        return [
            SmartSearchService::FIELD_ID    => self::VAR_INT,
            SmartSearchService::FIELD_TITLE => self::VAR_STRING,
            SmartSearchService::FIELD_TYPE  => self::VAR_STRING,
            SmartSearchService::FIELD_BODY  => self::VAR_STRING,
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    #[ArrayShape([
        SmartSearchService::FIELD_TITLE => "mixed|string",
        SmartSearchService::FIELD_TYPE  => "mixed|string",
        SmartSearchService::FIELD_BODY  => "false|string"
    ])]
    public function inverseTransform(array $data): array
    {
        return [
            SmartSearchService::FIELD_TITLE => $data[SmartSearchService::FIELD_TITLE] ?? '',
            SmartSearchService::FIELD_TYPE  => $data[SmartSearchService::FIELD_TYPE] ?? '',
            SmartSearchService::FIELD_BODY  => $data[SmartSearchService::FIELD_BODY] ?? '{}',
        ];
    }
}
