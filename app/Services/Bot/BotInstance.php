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

namespace medcenter24\mcCore\App\Services\Bot;


/**
 * Configurable instance of the bot
 * Class BotInstance
 * @package medcenter24\mcCore\App\Services\Bot
 */
class BotInstance
{
    /**
     * Loading BotInterface by the configuration
     * @param string $connection
     * @return Bot
     * @throws \ErrorException
     */
    public function getBot( $connection = '' ): Bot
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
