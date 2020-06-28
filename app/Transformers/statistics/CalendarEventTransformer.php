<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Transformers\statistics;

use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Transformers\AbstractTransformer;

class CalendarEventTransformer extends AbstractTransformer
{
    public function transform(Model $model): array
    {
        $fields = parent::transform($model);
        $fields['statusTitle'] = $model->getAttribute('accidentStatus')
            ? $model->getAttribute('accidentStatus')->getAttribute('title')
            : '';
        $fields['title'] = $fields['title'] ?: $model->getAttribute(AccidentService::FIELD_REF_NUM);
        return $fields;
    }

    /**
     * @inheritDoc
     */
    protected function getMap(): array
    {
        return [
            AccidentService::FIELD_ID,
            AccidentService::FIELD_TITLE,
            'start' => AccidentService::FIELD_CREATED_AT,
            'end' => AccidentService::FIELD_HANDLING_TIME,
            'status' => AccidentService::FIELD_ACCIDENT_STATUS_ID,
        ];
    }

    /**
     * @return array
     */
    protected function getMappedTypes(): array
    {
        return [
            AccidentService::FIELD_ID => self::VAR_INT,
            AccidentService::FIELD_CREATED_AT => self::VAR_DATETIME,
            AccidentService::FIELD_HANDLING_TIME => self::VAR_DATETIME,
            AccidentService::FIELD_ACCIDENT_STATUS_ID => self::VAR_INT,
        ];
    }
}
