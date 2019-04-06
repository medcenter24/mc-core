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

namespace App\Models\Telegram\Replies;


use App\Services\InviteService;
use App\TelegramUser;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

/**
 * Work with response on the invite reply
 * Class TelegramInviteReply
 * @package App\Models\Telegram\Replies
 */
class TelegramInviteReply
{
    /**
     * @var Message
     */
    private $message;

    /**
     * @var InviteService
     */
    private $inviteService;

    /**
     * @var null|Update
     */
    private $update;

    /**
     * TelegramInviteReply constructor.
     * @param Update|null $update
     * @param InviteService $service
     * @throws \Exception
     */
    public function __construct(Update $update = null, InviteService $service)
    {
        $this->update = $update;
        $this->message = $this->update->getMessage();
        \App::setLocale($this->message->from->languageCode);
        $this->inviteService = $service;
        if ($this->message) {
            $this->bindAccount();
        }
    }

    /**
     * @throws \Exception
     */
    private function bindAccount()
    {
        $token = $this->inviteSeeker();
        if ($token) {
            if ($this->inviteService->isValidInvite($token)) {
                $invite = $this->inviteService->getInviteByToken($token);
                $user = $invite->user;
                $telegramUser = TelegramUser::create([
                    'telegram_id' => $this->message->from->id,
                    'user_id' => $user->id,
                    'username' => (string)$this->message->from->username,
                    'first_name' => (string)$this->message->from->firstName,
                    'last_name' => (string)$this->message->from->lastName,
                ]);

                \Log::info('TelegramUser was assigned to the our user',
                    [
                        'telegramId' => $telegramUser->telegram_id,
                        'userId' => $telegramUser->user_id,
                        'username' => $telegramUser->username
                    ]);

                $invite->delete();

                \Telegram::sendMessage([
                    'chat_id' => $this->message->from->id,
                    'text' => trans('telegram.invite_success'),
                ]);
            } else {
                \Log::info('Incorrect invite token', [
                    'update' => $this->update
                ]);
                \Telegram::sendMessage([
                    'chat_id' => $this->message->from->id,
                    'text' => trans('telegram.incorrect_invite_token'),
                ]);
            }
        }
    }

    /**
     * @return bool|string
     */
    private function inviteSeeker()
    {
        $token = false;
        $parentMessage = $this->message->replyToMessage;
        $from = $this->message->from;
        if ($from && $parentMessage
            && $parentMessage->text == trans('telegram.invite_request', [], $from->languageCode)) {
            $token = $this->message->text;
        }
        return $token;
    }
}
