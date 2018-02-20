<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Admin\Telegram;


use App\Http\Controllers\AdminController;
use App\Http\Requests\Telegram\SendMessage;
use App\Services\Bot\BotInstance;
use Telegram;

class MessageController extends AdminController
{
    /**
     * @param SendMessage $request
     * @param BotInstance $botInstance
     * @return \Illuminate\Http\JsonResponse
     * @throws \ErrorException
     */
    public function send(SendMessage $request, BotInstance $botInstance)
    {
        /** @see \Telegram\Bot\Objects\Message $response */
        $messageId = $botInstance->getBot('telegram')->sendTextMessage([
                // my user id, to find it I will need to got to the channel @userinfobot
                'chat_id' => $request->input('receiverId', 0), // '344795925',
                'text' => $request->input('msg', 0),
            ]);

        return response()->json([compact('messageId')]);
    }
}
