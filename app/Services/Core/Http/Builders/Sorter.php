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

namespace medcenter24\mcCore\App\Services\Core\Http\Builders;


use Illuminate\Support\Collection;

class Sorter extends RequestBuilder
{
    private const DIRECTIONS = ['asc', 'desc'];

    /**
     * @var Collection
     */
    private $sortBy;

    public function inject(array $config): void
    {
        parent::inject($config);
        $this->sortBy = $this->getFields()->whereIn(self::FIELD_VALUE, self::DIRECTIONS);
    }

    public function getSortBy(): Collection
    {
        return $this->sortBy ?? collect();
    }
}