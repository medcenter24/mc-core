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
use medcenter24\mcCore\App\Services\Entity\DoctorAccidentService;

class DoctorAccidentStatusTransformer extends AbstractTransformer
{
    public function transform(Model $model): array
    {
        $fields = parent::transform($model);
        $fields['hasParent'] = $model->getAttribute('accident')
            && $model->getAttribute('accident')->getAttribute('parent_id');

        return $fields;
    }

    protected function getMap(): array
    {
        return [
            DoctorAccidentService::FIELD_ID,
        ];
    }
}
