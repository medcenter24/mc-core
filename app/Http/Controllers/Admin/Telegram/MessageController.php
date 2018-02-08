<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Admin\Telegram;


use App\Http\Controllers\AdminController;
use App\Http\Requests\Telegram\SendMessage;
use Telegram;

class MessageController extends AdminController
{
    public function send(SendMessage $request)
    {
        /** @see \Telegram\Bot\Objects\Message $response */
        $response = Telegram::sendMessage([
            // my user id, to find it I will need to got to the channel @userinfobot
            'chat_id' => $request->input('receiverId', 0), // '344795925',
            'text' => $request->input('msg', 0),
        ]);

        $messageId = $response->getMessageId();
        return response()->json([compact('messageId')]);
    }
}
