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


use medcenter24\mcCore\App\AccidentStatusHistory;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Helpers\Date;
use medcenter24\mcCore\App\Helpers\MediaHelper;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\LogoService;
use medcenter24\mcCore\App\Services\UserService;
use medcenter24\mcCore\App\User;

class AccidentStatusHistoryTransformer extends AbstractTransformer
{
    use ServiceLocatorTrait;

    /**
     * @param AccidentStatusHistory $history
     * @return array
     * @throws InconsistentDataException
     */
    public function transform(AccidentStatusHistory $history): array
    {
        return [
            'id' => $history->id,
            'user_id' => $history->user_id,
            'user_name' => $history->user_id ? $history->user->name : '',
            'user_thumb' => $history->user_id && $history->user->hasMedia(LogoService::FOLDER)
                ? MediaHelper::b64($history->user, LogoService::FOLDER, User::THUMB_45) : '',
            'accident_status_id' => $history->accident_status_id,
            'status' => $history->accidentStatus->title,
            'commentary' => $history->commentary,
            'created_at' => Date::sysDate(
                $history->getAttribute('created_at'),
                $this->getServiceLocator()->get(UserService::class)->getTimezone()
            ),
            'updated_at' => Date::sysDate(
                $history->getAttribute('updated_at'),
                $this->getServiceLocator()->get(UserService::class)->getTimezone()
            ),
        ];
    }
}
