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

namespace medcenter24\mcCore\App\Services\CaseServices\AccidentStatusVisor;

use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\App\Services\Entity\HospitalAccidentService;

class HospitalCaseVisorService extends AbstractVisorService
{
    protected function getStatusMap(): array
    {
        return [
            HospitalAccidentService::FIELD_HOSPITAL_ID => [
                self::STATUS_TITLE => AccidentStatusService::STATUS_ASSIGNED,
                self::STATUS_TYPE => AccidentStatusService::TYPE_HOSPITAL,
            ],
            HospitalAccidentService::FIELD_HOSPITAL_GUARANTEE_ID => [
                self::STATUS_TITLE => AccidentStatusService::STATUS_HOSPITAL_GUARANTEE,
                self::STATUS_TYPE => AccidentStatusService::TYPE_HOSPITAL,
            ],
            HospitalAccidentService::FIELD_HOSPITAL_INVOICE_ID => [
                self::STATUS_TITLE => AccidentStatusService::STATUS_HOSPITAL_INVOICE,
                self::STATUS_TYPE => AccidentStatusService::TYPE_HOSPITAL,
            ],
        ];
    }
}