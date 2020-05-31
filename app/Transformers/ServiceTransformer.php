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
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\App\Services\Entity\ServiceService;
use medcenter24\mcCore\App\Transformers\Traits\DiseasableTransformer;
use medcenter24\mcCore\App\Transformers\Traits\UserTypeTransformer;

class ServiceTransformer extends AbstractTransformer
{
    use DiseasableTransformer;
    use UserTypeTransformer;

    /**
     * @param Model|Service $model
     * @return array
     */
    public function transform(Model $model): array
    {
        $fields = parent::transform($model);
        $fields['type'] = $this->getCreatorUserType($model);
        $fields['diseases'] = $this->getTransformedDiseases($model->getAttribute('diseases'));
        return $fields;
    }

    protected function getMap(): array
    {
        return [
            ServiceService::FIELD_ID,
            ServiceService::FIELD_TITLE,
            ServiceService::FIELD_DESCRIPTION,
            ServiceService::FIELD_STATUS,
            'diseases',
        ];
    }

    protected function getMappedTypes(): array
    {
        return [
            ServiceService::FIELD_ID => self::VAR_INT,
        ];
    }

    public function inverseTransform(array $data): array
    {
        $transformed = parent::inverseTransform($data);
        $transformed = $this->inverseDiseasesTransform($transformed);
        return $transformed;
    }
}
