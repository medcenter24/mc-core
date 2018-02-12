<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
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
    protected $description = 'Start to use the bot';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {

        $user = $this->getUpdate()->getMessage()->getFrom();
        // \Log::info('i', [$user->id, $user->username, $user->languageCode, $user->first_name, $user->last_name]);

        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $telegramUser = TelegramUser::where('telegram_id', $user->id)->first();
        if (!$telegramUser) {
            $this->replyWithMessage(['text' => trans('telegram.welcome_guest')]);
            $this->replyWithMessage(['text' => trans('telegram.invite_request')]);
        }

        $commands = $this->telegram->getCommands();

        $text = '';
        foreach ($commands as $name => $handler) {
            $text .= sprintf('/%s - %s'.PHP_EOL, $name, $handler->getDescription());
        }

        $this->replyWithMessage(compact('text'));

        // Trigger another command dynamically from within this command
        // When you want to chain multiple commands within one or process the request further.
        // The method supports second parameter arguments which you can optionally pass, By default
        // it'll pass the same arguments that are received for this command originally.
        // $this->triggerCommand('subscribe');
    }
}