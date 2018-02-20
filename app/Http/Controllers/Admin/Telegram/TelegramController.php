<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Admin\Telegram;


use App\Http\Controllers\AdminController;
use App\Services\Bot\BotInstance;
use Telegram;

class TelegramController extends AdminController
{
    /**
     * @param BotInstance $botInstance
     * @return \Illuminate\Http\JsonResponse
     * @throws \ErrorException
     */
    public function getMe(BotInstance $botInstance)
    {
        $telegram = $botInstance->getBot('telegram');
        $info = $telegram->getBotInformation();
        return response()->json($info);
    }
}
