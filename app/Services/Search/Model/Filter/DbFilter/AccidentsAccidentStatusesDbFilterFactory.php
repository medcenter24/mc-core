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

namespace medcenter24\mcCore\App\Services\Search\Model\Filter\DbFilter;

use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\App\Services\Search\Model\SearchJoin;
use medcenter24\mcCore\App\Services\Search\Model\SearchWhere;

class AccidentsAccidentStatusesDbFilterFactory extends AbstractDbFilterFactory
{
    use FilterTraitInId;

    protected function getTableName(): string
    {
        return 'accident_statuses';
    }

    protected function getJoins(): array
    {
        return [
            new SearchJoin(
                'accidents',
                $this->getTableName(),
                AccidentService::FIELD_ACCIDENT_STATUS_ID,
                AccidentStatusService::FIELD_ID,
            )
        ];
    }

    protected function getWheres(mixed $whereValue): array
    {
        return [
            new SearchWhere(
                $this->getTableName(),
                AccidentStatusService::FIELD_ID,
                $this->getValues($whereValue),
                $this->getWhereOperation(),
            )
        ];
    }
}
