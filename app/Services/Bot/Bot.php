<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
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
