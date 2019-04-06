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

namespace App\Services\Bot;

/**
 * Defines bot interface and things that it should do
 * Interface Bot
 * @package App\Services\Bot
 */
interface Bot
{
    /**
     * Send text to the user
     * @param array $settings
     *  chat_id => unique source where to I need to send message
     *  text => message to be sent
     * @return messageId
     */
    public function sendTextMessage(array $settings = []);

    /**
     * Set to the bot URL which will be used as callable entry point to inform about action
     * @param array $conf
     *  url - webhook url
     *  certificate - to have encrypted requests
     * @return bool
     */
    public function setWebhook(array $conf = []);

    /**
     * Returns information about current configuration of the webhook
     * @return array
     * 'webhookUrl' => callback url
     * 'hasCertificate' => true/false if certificate provided
     * 'pendingUpdateCount' => how many times try to resend info
     * 'lastErrorDate' => date
     * 'maxConnections' => connections
     * 'allowedUpdates' => if allowed direct request to get all data (to avoid webhook)
     */
    public function getWebhookInformation();

    /**
     * @return mixed
     */
    public function removeWebhook();

    /**
     * Load information about current Bot
     * @return array
     * 'id' => Telegram identifier
     * 'isBot' => True, if this user is a bot,
     * 'firstName' => name,
     * 'lastName' => surname,
     * 'username' => login,
     */
    public function getBotInformation();
}
