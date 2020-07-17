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

namespace medcenter24\mcCore\App\Transformers;

use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\App\Services\Entity\ScenarioService;
use medcenter24\mcCore\App\Transformers\Traits\CaseTypeTransformer;

class ScenarioTransformer extends AbstractTransformer
{
    use CaseTypeTransformer;

    public function transform (Model $model): array
    {
        $fields = parent::transform($model);
        $fields['tag'] = $this->getTransformedType($fields['tag']);
        $fields['status'] = $model->getAttribute('status') ?: '';

        $accidentStatus = $model->getAttribute('accidentStatus');
        $fields['title'] = $accidentStatus ? $accidentStatus->getAttribute(AccidentStatusService::FIELD_TITLE) : '';
        $fields['accidentStatusType'] = $accidentStatus ? $accidentStatus->getAttribute(AccidentStatusService::FIELD_TYPE) : '';

        return $fields;
    }

    /**
     * @param array $data
     * @return array
     * @throws InconsistentDataException
     */
    public function inverseTransform(array $data): array
    {
        $data = parent::inverseTransform($data);
        $data['tag'] = $this->getInverseTransformedType($data['tag']);
        return $data;
    }

    protected function getMap(): array
    {
        return [
            ScenarioService::FIELD_ID,
            ScenarioService::FIELD_TAG,
            ScenarioService::FIELD_ORDER,
            ScenarioService::FIELD_MODE,
            'accidentStatusId' => ScenarioService::FIELD_ACCIDENT_STATUS_ID,
        ];
    }

    protected function getMappedTypes(): array
    {
        return [
            ScenarioService::FIELD_ID => self::VAR_INT,
            ScenarioService::FIELD_ORDER => self::VAR_INT,
            ScenarioService::FIELD_ACCIDENT_STATUS_ID => self::VAR_INT,
        ];
    }
}
