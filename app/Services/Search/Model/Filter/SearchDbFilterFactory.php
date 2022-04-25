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

namespace medcenter24\mcCore\App\Services\Search\Model\Filter;

use Illuminate\Support\Str;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;

class SearchDbFilterFactory
{
    use ServiceLocatorTrait;

    public function create(SearchFilter $filter, string $srcTable): SearchDbFilter
    {
        return $this->getDbFilter($srcTable, $filter);
    }

    private function getDbFilter(string $srcTable, SearchFilter $filter): SearchDbFilter
    {
        $table = Str::ucfirst($srcTable);
        $model = Str::ucfirst(Str::camel($filter->getModel()));
        $namespace = 'medcenter24\\mcCore\\App\\Services\\Search\\Model\\Filter\\DbFilter\\';
        $class = $namespace.$table.$model.'DbFilterFactory';
        return $this->getServiceLocator()->get($class)->create($filter->getValues());
    }
}
