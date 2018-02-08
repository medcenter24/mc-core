<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Telegram\Commands;


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
        \Log::info('Im here but dont work');
        $this->replyWithMessage(['text' => 'Hello! Welcome to our bot, Here are our available commands:']);

        $this->replyWithChatAction(['action' => Actions::TYPING]);

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