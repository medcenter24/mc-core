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

use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Helpers\Date;
use medcenter24\mcCore\App\Helpers\MediaHelper;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\LogoService;
use medcenter24\mcCore\App\Services\Entity\UserService;
use Cmgmyr\Messenger\Models\Message;

class MessageTransformer extends TransformerAbstract
{
    use ServiceLocatorTrait;

    /**
     * @param Message $message
     * @return array
     * @throws InconsistentDataException
     */
    public function transform(Message $message): array
    {
        return [
            'id' => $message->id,
            'userId' => $message->user_id,
            'userName' => $message->user_id ? $message->user->name : '',
            'userThumb' => $message->user_id && $message->user->hasMedia(LogoService::FOLDER)
                ? MediaHelper::b64($message->user, LogoService::FOLDER, UserService::THUMB_45) : '',
            'body' => $message->body,
            'createdAt' => Date::sysDate(
                $message->created_at,
                $this->getServiceLocator()->get(UserService::class)->getTimezone()
            ),
        ];
    }
}
