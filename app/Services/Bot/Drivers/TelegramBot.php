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

namespace medcenter24\mcCore\App\Services\Bot\Drivers;


use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramBot extends AbstractBotDriver
{
    /**
     * @return Api
     * @throws TelegramSDKException
     */
    protected function getTelegram()
    {
        return new Api($this->getConfiguration()->get('bots.' . $this->getConfiguration()->get('default').'.token'));
    }

    /**
     * @return array
     * @throws TelegramSDKException
     */
    public function getBotInformation()
    {
        $response = $this->getTelegram()->getMe();
        return [
            // Unique identifier for this user or bot.
            'id' => $response->getId(),
            // True, if this user is a bot
            'isBot' => $response->getIsBot(),
            'firstName' => $response->getFirstName(),
            'lastName' => $response->getLastName(),
            'username' => $response->getUsername(),
        ];
    }

    /**
     * @return array
     * @throws TelegramSDKException
     */
    public function getWebhookInformation()
    {
        $info = $this->getTelegram()->getWebhookInfo();
        return [
            'webhookUrl' => $info->getUrl(),
            'hasCertificate' => $info->getHasCustomCertificate(),
            'pendingUpdateCount' => $info->getPendingUpdateCount(),
            'lastErrorDate' => $info->getLasErrorDate(),
            'maxConnections' => $info->getMaxConnections(),
            'allowedUpdates' => $info->getAllowedUpdates(),
        ];
    }

    /**
     * @param array $settings
     * @return int|string
     * @throws TelegramSDKException
     */
    public function sendTextMessage(array $settings = [])
    {
        $message = $this->getTelegram()->sendMessage($settings);
        return $message->getMessageId();
    }

    /**
     * @param array $conf
     * @return bool
     * @throws TelegramSDKException
     */
    public function setWebhook(array $conf = [])
    {
        return $this->getTelegram()->setWebhook($conf);
    }

    /**
     * @return bool|mixed
     * @throws TelegramSDKException
     */
    public function removeWebhook()
    {
        return $this->getTelegram()->removeWebhook();
    }
}
