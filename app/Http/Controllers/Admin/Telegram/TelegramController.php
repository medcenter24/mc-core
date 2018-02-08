<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Admin\Telegram;


use App\Http\Controllers\AdminController;
use Telegram;

class TelegramController extends AdminController
{
    public function getMe()
    {
        $response = Telegram::getMe();

        return response()->json([
            // Unique identifier for this user or bot.
            'id' => $response->getId(),
            // True, if this user is a bot
            'isBot' => $response->getIsBot(),
            'firstName' => $response->getFirstName(),
            'lastName' => $response->getLastName(),
            'username' => $response->getUsername(),
        ]);
    }
}
