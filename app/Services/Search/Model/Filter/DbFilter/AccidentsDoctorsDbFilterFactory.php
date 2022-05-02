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

use medcenter24\mcCore\App\Services\Entity\AbstractModelService;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\DoctorAccidentService;
use medcenter24\mcCore\App\Services\Entity\DoctorService;
use medcenter24\mcCore\App\Services\Search\Model\SearchJoin;
use medcenter24\mcCore\App\Services\Search\Model\SearchWhere;

class AccidentsDoctorsDbFilterFactory extends AbstractDbFilterFactory
{
    use FilterTraitInId;

    protected function getTableName(): string
    {
        return 'doctors';
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
                $this->getTableName(),
                DoctorAccidentService::FIELD_DOCTOR_ID,
                DoctorService::FIELD_ID,
            ),
        ];
    }

    protected function getWheres(mixed $whereValue): array
    {
        return [
            new SearchWhere(
                'accidents',
                AccidentService::FIELD_CASEABLE_TYPE,
                AccidentService::CASEABLE_TYPE_DOCTOR,
            ),
            new SearchWhere(
                $this->getTableName(),
                DoctorService::FIELD_ID,
                $this->getValues($whereValue),
                $this->getWhereOperation(),
            )
        ];
    }
}
