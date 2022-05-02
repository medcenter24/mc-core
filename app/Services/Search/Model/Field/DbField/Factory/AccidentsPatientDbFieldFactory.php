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

use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\PatientService;
use medcenter24\mcCore\App\Services\Search\Model\SearchJoin;

class AccidentsPatientDbFieldFactory extends AbstractDbFieldFactory
{
    protected function getTableName(): string
    {
        return 'accidents';
    }

    /**
     * @return string
     */
    protected function getJoinTable(): string
    {
        return 'patients';
    }

    protected function getJoins(): array
    {
        return [
            new SearchJoin(
                $this->getTableName(),
                $this->getJoinTable(),
                AccidentService::FIELD_PATIENT_ID,
                PatientService::FIELD_ID,
            ),
        ];
    }

    protected function getSelectFieldParts(): array
    {
        return [$this->getJoinTable(), PatientService::FIELD_NAME];
    }
}
