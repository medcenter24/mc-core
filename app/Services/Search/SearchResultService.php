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

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\Search\Model\Field\PostQueryField\AbstractPostQueryField;
use medcenter24\mcCore\App\Services\Search\Model\Field\Request\SearchField;

class SearchResultService
{
    use ServiceLocatorTrait;

    public function getResultData(Collection $data, SearchRequest $searchRequest): Collection
    {
        return $this->addPostQueryFields($data, $searchRequest);
    }

    private function addPostQueryFields(Collection $data, SearchRequest $searchRequest): Collection
    {
        $count = 0;
        foreach ($searchRequest->getFields()->getFields() as $field) {
            $postQueryField = $this->getPostQueryField($field);
            if (!isset($postQueryField)) {
                $count++;
                continue;
            }
            $data = $postQueryField->apply($field, $data, $count++);
        }
        return $data;
    }

    private function getPostQueryField(SearchField $field): ?AbstractPostQueryField
    {
        $model = Str::ucfirst(Str::camel($field->getId()));
        $namespace = 'medcenter24\\mcCore\\App\\Services\\Search\\Model\\Field\\PostQueryField\\';
        $class = $namespace.$model.'PostQueryField';
        if (class_exists($class)) {
            /** @var AbstractPostQueryField $fieldLoader */
            return $this->getServiceLocator()->get($class);
        }
        return null;
    }
}
