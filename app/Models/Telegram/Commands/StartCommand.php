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

namespace App\Models\Telegram\Commands;


use App\TelegramUser;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected $name = 'start';

    /**
     * @var string Command Description
     */
    protected $description = 'telegram.command_start_description';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $user = $this->getUpdate()->getMessage()->getFrom();
        \App::setLocale($user->languageCode);
        $this->description = trans($this->description);
        // \Log::info('i', [$user->id, $user->username, $user->languageCode, $user->first_name, $user->last_name]);

        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $telegramUser = TelegramUser::where('telegram_id', $user->id)->first();
        if (!$telegramUser) {
            $this->replyWithMessage(['text' => trans('telegram.welcome_guest')]);
            \Telegram::sendMessage([
                'chat_id' => $user->id,
                'text' => trans('telegram.invite_request'),
                'reply_markup' => json_encode(['force_reply' => true]),
            ]);
        } else {
            $this->replyWithMessage(['text' => trans('telegram.welcome_user', ['user' => $telegramUser->user->name])]);
            $commands = $this->telegram->getCommands();
            $text = '';
            foreach ($commands as $name => $handler) {
                $text .= sprintf('/%s - %s'.PHP_EOL, $name, trans($handler->getDescription()));
            }
            $this->replyWithMessage(compact('text'));
        }
        // $this->replyWithChatAction()

        // Trigger another command dynamically from within this command
        // When you want to chain multiple commands within one or process the request further.
        // The method supports second parameter arguments which you can optionally pass, By default
        // it'll pass the same arguments that are received for this command originally.
        // $this->triggerCommand('subscribe');
    }
}