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

namespace medcenter24\mcCore\App\Services\Search;

use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\Search\Model\Field\SearchFieldsCollection;
use medcenter24\mcCore\App\Services\Search\Model\Filter\SearchFiltersCollection;
use Symfony\Component\HttpFoundation\ParameterBag;

class SearchRequest
{
    use ServiceLocatorTrait;

    private const PARAM_RESULT = 'result';
    private const PARAM_FILTERS = 'filters';
    private const PARAM_FIELDS = 'fields';

    private const RESULT_DATATABLE = 'datatable';
    private const RESULT_XLS = 'xls';

    private ParameterBag $parameterBag;
    private SearchFieldsCollection $searchFieldsCollection;
    private SearchFiltersCollection $searchFiltersCollection;

    public function getParameterBag(): ParameterBag
    {
        return $this->parameterBag;
    }

    public function getResultType(): string
    {
        return $this->getParameterBag()->get(self::PARAM_RESULT, self::RESULT_DATATABLE);
    }

    public function getFilters(): SearchFiltersCollection
    {
        if (!isset($this->searchFiltersCollection)) {
            $this->searchFiltersCollection = $this->getServiceLocator()->get(SearchFiltersCollection::class);
            $this->searchFiltersCollection->load($this->getParameterBag()->get(self::PARAM_FILTERS, []));
        }
        return $this->searchFiltersCollection;
    }

    public function getFields(): SearchFieldsCollection
    {
        if (!isset($this->searchFieldsCollection)) {
            $this->searchFieldsCollection = $this->getServiceLocator()->get(SearchFieldsCollection::class);
            $this->searchFieldsCollection->load($this->getParameterBag()->get(self::PARAM_FIELDS, []));
        }
        return $this->searchFieldsCollection;
    }

    public function load(ParameterBag $parameterBag): void
    {
        if (!isset($this->parameterBag)) {
            $this->parameterBag = $parameterBag;
        }
    }
}
