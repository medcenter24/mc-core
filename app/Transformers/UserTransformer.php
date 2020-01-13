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

namespace medcenter24\mcCore\App\Transformers;


use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Helpers\MediaHelper;
use medcenter24\mcCore\App\Services\LogoService;
use medcenter24\mcCore\App\User;

class UserTransformer extends AbstractTransformer
{

    /**
     * @param User $user
     * @return array
     * @throws InconsistentDataException
     */
    public function transform (User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name ?: $user->email,
            'email' => $user->email,
            'phone' => $user->phone,
            'lang' => $user->lang,
            'thumb200' => $user->hasMedia(LogoService::FOLDER)
                ? MediaHelper::b64($user, LogoService::FOLDER, User::THUMB_200) : '',
            'thumb45' => $user->hasMedia(LogoService::FOLDER)
                ? MediaHelper::b64($user, LogoService::FOLDER, User::THUMB_45) : '',
            'timezone' => $user->timezone,
        ];
    }
}
