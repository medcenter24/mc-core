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

namespace medcenter24\mcCore\App\Services\Search\Model\Filter\DbFilter\Factory;

use medcenter24\mcCore\App\Services\Entity\AbstractModelService;
use medcenter24\mcCore\App\Services\Search\Model\SearchGroupBy;
use medcenter24\mcCore\App\Services\Search\Model\SearchWhere;
use medcenter24\mcCore\App\Transformers\Traits\CaseTypeTransformer;

class AccidentsSearchInDeletedDbFilterFactory extends AbstractDbFilterFactory
{
    use CaseTypeTransformer;

    protected function getTableName(): string
    {
        return 'accidents';
    }

    /**
     * @param $whereValue
     * @return SearchWhere[]
     */
    protected function getWheres($whereValue): array
    {
        return [
            new SearchWhere(
                $this->getTableName(),
                AbstractModelService::FIELD_DELETED_AT,
                !$whereValue,
                'isNull'
            )
        ];
    }

    protected function getGroupBy(): array
    {
        return [
            new SearchGroupBy(
                'accidents',
                AbstractModelService::FIELD_ID,
            )
        ];
    }

    protected function getLoaded(mixed $whereValue): bool
    {
        return is_bool($whereValue);
    }
}
