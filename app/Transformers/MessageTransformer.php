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
use Cmgmyr\Messenger\Models\Message;
use League\Fractal\TransformerAbstract;

class MessageTransformer extends TransformerAbstract
{
    /**
     * @param Message $message
     * @return array
     * @throws \ErrorException
     */
    public function transform(Message $message)
    {
        return [
            'id' => $message->id,
            'user_id' => $message->user_id,
            'user_name' => $message->user_id ? $message->user->name : '',
            'user_thumb' => $message->user_id && $message->user->hasMedia(LogoService::FOLDER)
                ? MediaHelper::b64($message->user, LogoService::FOLDER, User::THUMB_45) : '',
            'body' => $message->body,
            'created_at' => $message->created_at->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat')),
        ];
    }
}
