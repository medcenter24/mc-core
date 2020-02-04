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

namespace medcenter24\mcCore\App\Services\Core\Http;


use Illuminate\Support\Facades\Request;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Filter;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Paginator;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Sorter;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;

class DatatableRequestBuilder
{
    use ServiceLocatorTrait;

    public const PAGINATOR = 'paginator';
    public const SORTER = 'sorter';
    public const FILTER = 'filter';

    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @var Sorter
     */
    private $sorter;

    /**
     * @var Filter
     */
    private $filter;

    public function __construct(Request $request) {
        $this->paginator = $this->getServiceLocator()->get(Paginator::class)->inject($request->json(self::PAGINATOR));
        $this->sorter = $this->getServiceLocator()->get(Sorter::class)->inject($request->json(self::SORTER));
        $this->filter = $this->getServiceLocator()->get(Filter::class)->inject($request->json(self::FILTER));
    }

    public function getPaginator(): Paginator {
        return $this->paginator;
    }

    public function getSorter(): Sorter {
        return $this->sorter;
    }

    public function getFilter(): Filter {
        return $this->filter;
    }
}
