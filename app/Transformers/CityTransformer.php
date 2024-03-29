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

use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\App\Services\Entity\CityService;

class CityTransformer extends AbstractTransformer
{
    private const REGION_ID = 'regionId';

    public function transform(Model $model): array
    {

        $fields = parent::transform($model);
        $fields['regionTitle'] = $model->getAttribute('region')
            ? $model->getAttribute('region')->getAttribute('title')
            : '';
        $fields['countryTitle'] = $model->getAttribute('region') && $model->getAttribute('region')->getAttribute('country')
            ? $model->getAttribute('region')->getAttribute('country')->getAttribute('title')
            : '';
        return $fields;
    }

    #[ArrayShape([
        0 => "string",
        1 => "string",
        self::REGION_ID => "string"
    ])]
    protected function getMap(): array
    {
        return [
            CityService::FIELD_ID,
            CityService::FIELD_TITLE,
            self::REGION_ID => CityService::FIELD_REGION_ID,
        ];
    }

    #[ArrayShape([
        CityService::FIELD_ID => "string",
        CityService::FIELD_REGION_ID => "string"
    ])] protected function getMappedTypes(): array
    {
        return [
            CityService::FIELD_ID        => self::VAR_INT,
            CityService::FIELD_REGION_ID => self::VAR_INT,
        ];
    }
}
