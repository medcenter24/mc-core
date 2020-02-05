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


use Illuminate\Database\Eloquent\Builder;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Filter;

class CaseSearchFieldsTransformer
{
    protected const FIELD_PATIENT_NAME = 'patientName';
    protected const FIELD_REF_NUM = 'refNum';
    protected const FIELD_ASSISTANT_REF_NUM = 'assistantRefNum';
    protected const FIELD_CREATED_AT = 'createdAt';
    protected const FIELD_STATUS = 'status';

    public function transform(Builder $eloquent, array $filters): array
    {
        foreach ($filters as $key => $filter) {
            switch ($filter[Filter::FIELD_NAME]) {
                case self::FIELD_PATIENT_NAME:
                    $eloquent->join('patients', 'accidents.patient_id', '=', 'patients.id');
                    $filter[Filter::FIELD_NAME] = 'patients.name';
                    break;
                case self::FIELD_REF_NUM:
                    $filter[Filter::FIELD_NAME] = 'ref_num';
                    break;
                case self::FIELD_ASSISTANT_REF_NUM:
                    $filter[Filter::FIELD_NAME] = 'assistant_ref_num';
                    break;
                case self::FIELD_CREATED_AT:
                    $filter[Filter::FIELD_NAME] = 'accidents.created_at';
                    break;
                case self::FIELD_STATUS:
                    $eloquent->join('accident_statuses', 'accidents.accident_status_id', '=', 'accident_statuses.id');
                    $filter[Filter::FIELD_NAME] = 'accident_statuses.title';
                    break;
            }
            $filters[$key] = $filter;
        }
        return $filters;
    }
}