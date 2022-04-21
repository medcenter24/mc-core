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

namespace medcenter24\mcCore\App\Services\Search\Model;

use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;

class SearchFiltersCollection
{
    use ServiceLocatorTrait;

    private Collection $filters;

    public function load(array $filters): void
    {
        $this->filters = collect();
        foreach ($filters as $model => $values) {

            $ids = [];
            foreach ($values as $value) {
                if (!empty($value['id'])) {
                    $ids[] = $value['id'];
                }
            }

            if (!empty($ids)) {
                $this
                    ->filters
                    ->push(
                        $this
                            ->getSearchFilterFactory()
                            ->create($model, $ids)
                    );
            }
        }
    }

    public function getFilters(): Collection
    {
        return $this->filters;
    }

    private function getSearchFilterFactory()
    {
        return $this->getServiceLocator()->get(SearchFilterFactory::class);
    }
}
