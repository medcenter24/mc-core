<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Telegram;


use App\Http\Controllers\Controller;
use App\Models\Telegram\Replies\DoctorCasePickupReply;
use App\Models\Telegram\Replies\TelegramInviteReply;
use App\Services\AccidentStatusesService;
use App\Services\InviteService;
use Illuminate\Support\Facades\Log;
use Telegram;

class TelegramApiController extends Controller
{
    /**
     * Callback for the telegrams requests
     *
     * @param InviteService $inviteService
     * @param AccidentStatusesService $accidentStatusesService
     * @return string
     * @throws \Exception
     */
    public function index(InviteService $inviteService, AccidentStatusesService $accidentStatusesService)
    {
        $updates = Telegram::commandsHandler(true);

        // if this is response on the invite request then I need to work with it
        new TelegramInviteReply($updates, $inviteService);
        new DoctorCasePickupReply($updates, $accidentStatusesService);

        Log::info('Turn Commands Handler on', ['updates' => $updates]);
        return 'ok';
    }
}
