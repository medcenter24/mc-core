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

namespace medcenter24\mcCore\App\Services\Search\Model\Field\DbField\Factory;

use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Services\Entity\AbstractModelService;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\DiagnosticService;
use medcenter24\mcCore\App\Services\Search\Model\SearchGroupBy;
use medcenter24\mcCore\App\Services\Search\Model\SearchJoin;
use medcenter24\mcCore\App\Services\Search\Model\SearchWhere;

class AccidentsDiagnosisDbFieldFactory extends AbstractDbFieldFactory
{
    protected function getTableName(): string
    {
        return 'diagnostics';
    }

    protected function getJoins(): array
    {
        return [
            new SearchJoin(
                'accidents',
                'doctor_accidents',
                AccidentService::FIELD_CASEABLE_ID,
                AbstractModelService::FIELD_ID,
            ),
            new SearchJoin(
                'doctor_accidents',
                'diagnosticables',
                AbstractModelService::FIELD_ID,
                'diagnosticable_id',
            ),
            new SearchJoin(
                'diagnosticables',
                $this->getTableName(),
                'diagnostic_id',
                AbstractModelService::FIELD_ID,
            ),
        ];
    }

    protected function getWheres(): array
    {
        return [
            new SearchWhere(
                'diagnosticables',
                'diagnosticable_type',
                DoctorAccident::class,
            ),
        ];
    }

    protected function getSelectFieldParts(): array
    {
        return [$this->getTableName(), DiagnosticService::FIELD_TITLE];
    }

    protected function getSelectField(): string
    {
        $fields = $this->getSelectFieldParts();
        // pgsql only:
        return sprintf("string_agg(%s.%s, ',')", $fields[0], $fields[1]);
    }

    protected function getGroupBy(): array
    {
        return [
            new SearchGroupBy(
                'accidents',
                AccidentService::FIELD_ID,
            ),
        ];
    }
}
