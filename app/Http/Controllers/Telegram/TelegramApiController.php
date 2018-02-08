<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Telegram;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Telegram;

class TelegramApiController extends Controller
{
    public function index()
    {
        $updates = Telegram::commandsHandler(true);
        Log::info('Turn Commands Handler on', ['updates' => $updates]);
        return 'ok';
    }
}
