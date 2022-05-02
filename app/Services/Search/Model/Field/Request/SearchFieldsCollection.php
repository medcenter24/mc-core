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

namespace medcenter24\mcCore\App\Services\Search\Model\Field\Request;

use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;

class SearchFieldsCollection
{
    use ServiceLocatorTrait;

    private Collection $fields;

    public function load(array $fields): void
    {
        $this->fields = collect();
        foreach ($fields as $field) {
            $this->fields->push($this->getSearchFieldFactory()->create($field));
        }
    }

    public function getFields(): Collection
    {
        return $this->fields;
    }

    private function getSearchFieldFactory(): SearchFieldFactory
    {
        return $this->getServiceLocator()->get(SearchFieldFactory::class);
    }
}
