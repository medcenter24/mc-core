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

namespace medcenter24\mcCore\App\Services\ApiSearch;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Filter;

class SearchFieldLogic
{
    public function getInternalFieldName(string $field): string
    {
        return Str::snake($field);
    }

    public function getExternalFieldName(string $field): string
    {
        return Str::camel($field);
    }

    /**
     * @param array $field // changeable filter
     * @return array // transformed and prepared filters (builder will be changes by the link)
     */
    public function transformFieldToInternalFormat(array $field): array
    {
        $field[Filter::FIELD_NAME] = $this->getInternalFieldName($field[Filter::FIELD_NAME]);
        return $field;
    }

    /**
     * @param array $filters
     * @return array
     */
    public function transformFieldsToExternalFormat(array $filters): array
    {
        foreach ($filters as $key => $filter) {
            $filter[Filter::FIELD_NAME] = $this->getExternalFieldName($filter[Filter::FIELD_NAME]);
            $filters[$key] = $filter;
        }
        return $filters;
    }

    /**
     * @return Collection of \medcenter24\mcCore\App\Models\Database\Relation
     */
    public function getRelations(): Collection
    {
        return collect();
    }
}
