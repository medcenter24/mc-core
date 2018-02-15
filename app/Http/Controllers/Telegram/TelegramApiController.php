<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Telegram;


use App\Http\Controllers\Controller;
use App\Models\Telegram\Replies\TelegramInviteReply;
use App\Services\InviteService;
use Illuminate\Support\Facades\Log;
use Telegram;

class TelegramApiController extends Controller
{
    /**
     * Callback for the telegrams requests
     *
     * @param InviteService $inviteService
     * @return string
     * @throws Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function index(InviteService $inviteService)
    {
        $updates = Telegram::commandsHandler(true);

        // if this is response on the invite request then I need to work with it
        new TelegramInviteReply($updates, $inviteService);

        Log::info('Turn Commands Handler on', ['updates' => $updates]);
        return 'ok';
    }
}
