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
use medcenter24\mcCore\App\Services\Entity\DoctorAccidentService;

class AccidentsVisitTimeRangesDbFilterFactory extends AbstractDbFilterFactory
{
    use FilterTraitDateRange;

    protected function isJoin(): bool
    {
        return true;
    }

    protected function getJoinTable(): string
    {
        return 'doctor_accidents';
    }

    protected function getJoinFirst(): string
    {
        return AccidentService::FIELD_CASEABLE_ID;
    }

    protected function getJoinSecond(): string
    {
        return DoctorAccidentService::FIELD_ID;
    }

    protected function getWhereField(): string
    {
        return DoctorAccidentService::FIELD_VISIT_TIME;
    }
}
