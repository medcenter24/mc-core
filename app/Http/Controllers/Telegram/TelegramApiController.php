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

namespace medcenter24\mcCore\App\Http\Controllers\Telegram;


use Illuminate\Http\Response;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\Controller;
use medcenter24\mcCore\App\Models\Telegram\Replies\DoctorCasePickupReply;
use medcenter24\mcCore\App\Models\Telegram\Replies\TelegramInviteReply;
use Illuminate\Support\Facades\Log;
use \Telegram;

class TelegramApiController extends Controller
{
    /**
     * Callback for the telegrams requests
     * @return Response
     * @throws InconsistentDataException
     */
    public function index(): Response
    {
        $updates = Telegram::commandsHandler(true);

        // if this is response on the invite request then I need to work with it
        new TelegramInviteReply($updates);
        new DoctorCasePickupReply($updates);

        Log::info('Turn Commands Handler on', ['updates' => $updates]);
        return response('ok');
    }
}
