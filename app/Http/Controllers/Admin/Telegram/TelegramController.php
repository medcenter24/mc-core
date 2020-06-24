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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers\Admin\Telegram;

use ErrorException;
use Illuminate\Http\JsonResponse;
use medcenter24\mcCore\App\Http\Controllers\AdminController;
use medcenter24\mcCore\App\Services\Bot\BotInstance;
use Telegram;

class TelegramController extends AdminController
{
    /**
     * @param BotInstance $botInstance
     * @return JsonResponse
     * @throws ErrorException
     */
    public function getMe(BotInstance $botInstance): JsonResponse
    {
        $telegram = $botInstance->getBot('telegram');
        $info = $telegram->getBotInformation();
        return response()->json($info);
    }
}
