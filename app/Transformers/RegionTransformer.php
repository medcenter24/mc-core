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
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Transformers;

use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Entity\Region;
use medcenter24\mcCore\App\Services\Entity\RegionService;

class RegionTransformer extends AbstractTransformer
{
    public function transform(Model $model): array
    {
        $fields = parent::transform($model);
        $fields['countryTitle'] = $model->getAttribute('country')
            ? $model->getAttribute('country')->getAttribute('title')
            : '';
        return $fields;
    }

    protected function getMap(): array
    {
        return [
            RegionService::FIELD_ID,
            RegionService::FIELD_TITLE,
            'countryId' => RegionService::FIELD_COUNTRY_ID,
        ];
    }

    protected function getMappedTypes(): array
    {
        return [
            RegionService::FIELD_ID => self::VAR_INT,
            RegionService::FIELD_COUNTRY_ID => self::VAR_INT,
        ];
    }
}
