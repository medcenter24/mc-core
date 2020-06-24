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

declare(strict_types=1);

namespace medcenter24\mcCore\App\Services\Core\Http;

use medcenter24\mcCore\App\Services\Core\Http\Builders\Filter;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Paginator;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Sorter;

class DataLoaderRequestBuilder
{
    public const PAGINATOR = 'paginator';
    public const SORTER = 'sorter';
    public const FILTER = 'filter';

    /**
     * @var Paginator
     */
    private Paginator $paginator;

    /**
     * @var Sorter
     */
    private Sorter $sorter;

    /**
     * @var Filter
     */
    private Filter $filter;

    /**
     * @param Paginator $paginator
     */
    public function setPaginator(Paginator $paginator): void
    {
        $this->paginator = $paginator;
    }

    /**
     * @param Filter $filter
     */
    public function setFilter(Filter $filter): void
    {
        $this->filter = $filter;
    }

    /**
     * @param Sorter $sorter
     */
    public function setSorter(Sorter $sorter): void
    {
        $this->sorter = $sorter;
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

    public function getPage(): int
    {
        return (int) floor($this->getPaginator()->getOffset() / $this->getPaginator()->getLimit()) + 1;
    }
}
