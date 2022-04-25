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

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\Search\Model\Field\SearchDbField;
use medcenter24\mcCore\App\Services\Search\Model\Field\SearchDbFieldFactory;
use medcenter24\mcCore\App\Services\Search\Model\Field\SearchField;
use medcenter24\mcCore\App\Services\Search\Model\Field\SearchFieldsCollection;
use medcenter24\mcCore\App\Services\Search\Model\Filter\SearchDbFilter;
use medcenter24\mcCore\App\Services\Search\Model\Filter\SearchDbFilterFactory;
use medcenter24\mcCore\App\Services\Search\Model\Filter\SearchFilter;
use medcenter24\mcCore\App\Services\Search\Model\Filter\SearchFiltersCollection;

class SearchService
{
    use ServiceLocatorTrait;

    public const SOURCE_TABLE_ACCIDENT = 'accidents';

    public const FIELD_NPP = 'npp'; // todo add this field after the result responsed
    public const FIELD_PATIENT = 'patient';
    public const FIELD_CITY = 'city';
    public const FIELD_DOCTOR_INCOME = 'doctor-income';
    public const FIELD_ASSIST_REF_NUM = 'assist-ref-num';

    public const FIELDS_DB = [
        self::FIELD_PATIENT,
        self::FIELD_CITY,
        self::FIELD_DOCTOR_INCOME,
        self::FIELD_ASSIST_REF_NUM,
    ];


    public function search(SearchRequest $searchRequest): Collection
    {
        $srcTable = self::SOURCE_TABLE_ACCIDENT;
        $query = $this->createQuery($srcTable);

        $fields = $searchRequest->getFields();
        $this->addColumns($query, $fields);

        $filters = $searchRequest->getFilters();
        $this->addFilters($query, $filters);

        var_dump($query->toSql());die;

        return $query->get();
    }

    private function createQuery(string $srcTable): Builder
    {
        return DB::table($srcTable);
    }

    private function addColumns(Builder $query, SearchFieldsCollection $fields): void
    {
        /** @var SearchField $field */
        foreach ($fields->getFields() as $field) {
            $this->addField($query, $field);
        }
    }

    private function addField(Builder $query, SearchField $field): void
    {
        if (in_array($field->getId(), self::FIELDS_DB)) {
            $dbField = $this->getDbField($field, $query->from);

            $this->attachJoin($query, $dbField);

            $query->addSelect(DB::raw(sprintf(
                '`%s` as `%s`',
                $dbField->getJoinTable().'.'.$dbField->getSelectField(),
                $field->getId(),
            )));

            if ($dbField->hasOrder()) {
                $query->orderBy($dbField->getJoinTable().'.'.$dbField->getSelectField(), $dbField->getOrder());
            }
        }
    }

    private function attachJoin(Builder $query, SearchDbField|SearchDbFilter $rule): void
    {
        if($rule->hasJoin()) {
            $query->join(
                $rule->getJoinTable(),
                $query->from.'.'.$rule->getJoinFirst(),
                '=',
                $rule->getJoinTable().'.'.$rule->getJoinSecond(),
            );
        }
    }

    private function addFilters(Builder $query, SearchFiltersCollection $filters): void
    {
        /** @var SearchFilter $filter */
        foreach ($filters->getFilters() as $filter) {
            $this->addCondition($filter, $query);
        }
    }

    private function addCondition(SearchFilter $filter, Builder $query): void
    {
        $dbFilter = $this->getDbFilter($filter, $query->from);
        $query->where(
            $dbFilter->getWhereField(),
            $dbFilter->getWhereOperation(),
            $dbFilter->getWhereValue(),
        );
        $this->attachJoin($query, $dbFilter);

        if (!empty($dbFilter->getAndWhere())) {
            foreach ($dbFilter->getAndWhere() as $field => $where) {
                $query->where($field, '=', $where);
            }
        }
    }

    private function getDbFilter(SearchFilter $filter, string $fromTable): SearchDbFilter
    {
        return $this->getDbFilterFactory()->create($filter, $fromTable);
    }

    private function getDbFilterFactory(): SearchDbFilterFactory
    {
        return $this->getServiceLocator()->get(SearchDbFilterFactory::class);
    }

    private function getDbField(SearchField $field, string $fromTable): SearchDbField
    {
        return $this->getDbFieldFactory()->create($field, $fromTable);
    }

    private function getDbFieldFactory(): SearchDbFieldFactory
    {
        return $this->getServiceLocator()->get(SearchDbFieldFactory::class);
    }
}
