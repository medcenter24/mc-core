<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Bot;


/**
 * Configurable instance of the bot
 * Class BotInstance
 * @package App\Services\Bot
 */
class BotInstance
{
    /**
     * Loading BotInterface by the configuration
     * @param string $connection
     * @return Bot
     * @throws \ErrorException
     */
    public function getBot( $connection = '' )
    {
        $isActive = config('bot.connections.' . $connection . '.active', false);
        $className = config('bot.connections.'.$connection.'.class', '');

        if (!$isActive || !class_exists($className)) {
            throw new \ErrorException('Connection ' . $connection . ' not configured');
        }

        $defaultConfiguration = config('bot.connections.'.$connection.'.config', []);
        return new $className($defaultConfiguration);
    }
}
