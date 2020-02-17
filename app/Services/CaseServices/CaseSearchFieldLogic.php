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

namespace medcenter24\mcCore\App\Services\CaseServices;

use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Models\Database\Relation;
use medcenter24\mcCore\App\Services\ApiSearch\SearchFieldLogic;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Filter;

class CaseSearchFieldLogic extends SearchFieldLogic
{
    protected const FIELD_ID = 'id';
    protected const FIELD_PATIENT_NAME = 'patientName';
    protected const FIELD_ASSISTANT_REF_NUM = 'assistantRefNum';
    protected const FIELD_CREATED_AT = 'createdAt';
    protected const FIELD_STATUS = 'status';

    public function transformFieldToInternalFormat(array $filter): array
    {
        switch ($filter[Filter::FIELD_NAME]) {
            case self::FIELD_ID:
                $filter[Filter::FIELD_NAME] = 'accidents.id';
                break;
            case self::FIELD_PATIENT_NAME:
                $filter[Filter::FIELD_NAME] = 'patients.name';
                break;
            case self::FIELD_ASSISTANT_REF_NUM:
                $filter[Filter::FIELD_NAME] = 'assistant_ref_num';
                break;
            case self::FIELD_CREATED_AT:
                $filter[Filter::FIELD_NAME] = 'accidents.created_at';
                break;
            case self::FIELD_STATUS:
                $filter[Filter::FIELD_NAME] = 'accident_statuses.title';
                break;
            default:
                $filter[Filter::FIELD_NAME] = $this->getInternalFieldName($filter[Filter::FIELD_NAME]);
        }
        return $filter;
    }

    public function getRelations(): Collection
    {
        return collect([
            new Relation('patients', 'accidents.patient_id', '=', 'patients.id', 'left'),
            new Relation('accident_statuses', 'accidents.accident_status_id', '=', 'accident_statuses.id', 'left'),
        ]);
    }
}