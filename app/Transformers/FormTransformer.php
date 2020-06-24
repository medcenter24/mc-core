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
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Services\Entity\FormService;
use medcenter24\mcCore\App\Transformers\Traits\CaseTypeTransformer;

class FormTransformer extends AbstractTransformer
{
    use CaseTypeTransformer;

    protected function getMap(): array
    {
        return [
            FormService::FIELD_ID,
            FormService::FIELD_TITLE,
            FormService::FIELD_DESCRIPTION,
            'formableType' => FormService::FIELD_FORMABLE_TYPE,
            FormService::FIELD_TEMPLATE,
        ];
    }

    /**
     * @inheritDoc
     */
    public function transform (Model $model): array
    {
        $fields = parent::transform($model);
        $fields['formableType'] = 'accident';
        return $fields;
    }

    public function inverseTransform(array $data): array
    {
        $data = parent::inverseTransform($data);
        $data[FormService::FIELD_FORMABLE_TYPE] = Accident::class;
        return $data;
    }
}
