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
use medcenter24\mcCore\App\Entity\User;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Helpers\MediaHelper;
use medcenter24\mcCore\App\Services\Entity\DoctorService;
use medcenter24\mcCore\App\Services\Entity\UserService;
use medcenter24\mcCore\App\Services\LogoService;

class DoctorProfileTransformer extends AbstractTransformer
{
    /**
     * @param Model|Doctor $model
     * @return array
     * @throws InconsistentDataException
     */
    public function transform (Model|Doctor $model): array
    {
        $fields = parent::transform($model);
        $fields['thumb200'] = $this->hasMedia($model->user) ?
            MediaHelper::b64($model->user, LogoService::FOLDER, UserService::THUMB_200)
            : '';
        $fields['thumb45'] = $this->hasMedia($model->user)
            ? MediaHelper::b64($model->user, LogoService::FOLDER, UserService::THUMB_45)
            : '';
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

    private function hasMedia(Model $model): bool
    {
        return $model instanceof User && $model->hasMedia(LogoService::FOLDER);
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
