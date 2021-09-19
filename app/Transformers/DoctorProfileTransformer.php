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
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Services\Entity\DoctorService;
use medcenter24\mcCore\App\Services\Entity\UserService;

class DoctorProfileTransformer extends AbstractTransformer
{
    /**
     * @param Model $model
     * @return array
     */
    public function transform (Model $model): array
    {
        $fields = parent::transform($model);
        $fields['pictureUrl'] = $model instanceof Doctor && $model->hasMedia()
            ? $model->getMedia('photo')->first()->getUrl('thumb') : '';
        $fields['city'] = $model->getAttribute('city')
            ? $model->getAttribute('city')->getAttribute('title')
            : '';
        $fields['phones'] = $model->getAttribute('user')
            ? $model->getAttribute('user')->getAttribute(UserService::FIELD_PHONE)
            : '';
        $fields['lang'] = $model->getAttribute('user')
            ? $model->getAttribute('user')->getAttribute('lang')
            : '';
        return $fields;
    }

    /**
     * @return array
     */
    protected function getMap(): array
    {
        return [
            DoctorService::FIELD_NAME,
            DoctorService::FIELD_MEDICAL_BOARD_NUM,
        ];
    }
}
