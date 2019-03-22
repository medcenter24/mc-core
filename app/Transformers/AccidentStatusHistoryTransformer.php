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


use App\AccidentStatusHistory;
use App\Helpers\MediaHelper;
use App\Services\LogoService;
use App\User;
use League\Fractal\TransformerAbstract;

class AccidentStatusHistoryTransformer extends TransformerAbstract
{
    /**
     * @param AccidentStatusHistory $history
     * @return array
     * @throws \ErrorException
     */
    public function transform(AccidentStatusHistory $history)
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
            'created_at' => $history->created_at->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat')),
            'updated_at' => $history->updated_at->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat')),
        ];
    }
}
