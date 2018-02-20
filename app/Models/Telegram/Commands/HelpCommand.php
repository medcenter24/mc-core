<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Telegram\Commands;


use Telegram\Bot\Commands\Command;

/**
 * Class HelpCommand.
 */
class HelpCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'help';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['listcommands'];

    /**
     * @var string Command Description
     */
    protected $description = 'telegram.command_help_description';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $user = $this->getUpdate()->getMessage()->getFrom();
        \App::setLocale($user->languageCode);
        $this->replyWithMessage(['text' => trans('telegram.help_account')]);

        $this->description = trans($this->description);

        $commands = $this->telegram->getCommands();

        $text = '';
        foreach ($commands as $name => $handler) {
            $text .= sprintf('/%s - %s'.PHP_EOL, $name, trans($handler->getDescription()));
        }

        $this->replyWithMessage(compact('text'));
    }
}
