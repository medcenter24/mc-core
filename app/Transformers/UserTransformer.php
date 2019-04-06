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

namespace App\Transformers;


use App\Helpers\MediaHelper;
use App\Services\LogoService;
use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * @param User $user
     * @return array
     * @throws \ErrorException
     */
    public function transform (User $user)
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
