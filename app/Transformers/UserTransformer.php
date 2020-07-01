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
use medcenter24\mcCore\App\Entity\User;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Helpers\MediaHelper;
use medcenter24\mcCore\App\Services\Entity\UserService;
use medcenter24\mcCore\App\Services\LogoService;

class UserTransformer extends AbstractTransformer
{
    /**
     * @param Model $model
     * @return array
     * @throws InconsistentDataException
     */
    public function transform (Model $model): array
    {
        $fields = parent::transform($model);
        $fields['thumb200'] = $this->hasMedia($model) ?
            MediaHelper::b64($model, LogoService::FOLDER, UserService::THUMB_200)
            : '';
        $fields['thumb45'] = $this->hasMedia($model)
            ? MediaHelper::b64($model, LogoService::FOLDER, UserService::THUMB_45)
            : '';
        return $fields;
    }

    private function hasMedia(Model $model): bool
    {
        return $model instanceof User && $model->hasMedia(LogoService::FOLDER);
    }

    protected function getMap(): array
    {
        return [
            UserService::FIELD_ID,
            UserService::FIELD_NAME,
            UserService::FIELD_EMAIL,
            UserService::FIELD_PHONE,
            UserService::FIELD_LANG,
            UserService::FIELD_TIMEZONE,
        ];
    }

    public function inverseTransform(array $data): array
    {
        $transformed = parent::inverseTransform($data);

        if (array_key_exists(UserService::FIELD_PASSWORD, $data)) {
            $transformed['password'] = bcrypt($data['password']);
        }
        return $transformed;
    }
}
