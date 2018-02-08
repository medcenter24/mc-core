<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Telegram\Commands;


use Telegram\Bot\Commands\Command;

class HelloCommand extends Command
{
    protected $name = 'initialization';

    protected $aliases = ['init'];

    /**
     * @var string Command Description
     */
    protected $description = 'Initialize new user to assign it to the system with invite key';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $commands = $this->telegram->getCommands();

        $text = '';
        foreach ($commands as $name => $handler) {
            $text .= sprintf('/%s - %s'.PHP_EOL, $name, $handler->getDescription());
        }

        $this->replyWithMessage(compact('text'));
    }
}