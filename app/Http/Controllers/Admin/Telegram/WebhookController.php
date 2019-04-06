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

namespace App\Http\Controllers\Admin\Telegram;


use App\Http\Controllers\AdminController;
use App\Http\Requests\Telegram\SetWebhook;
use App\Services\Bot\BotInstance;
use Telegram;
use Telegram\Bot\Exceptions\TelegramSDKException;

class WebhookController extends  AdminController
{

    /**
     * @param BotInstance $botInstance
     * @return \Illuminate\Http\JsonResponse
     * @throws \ErrorException
     */
    public function index (BotInstance $botInstance)
    {
        $telegram = $botInstance->getBot('telegram');
        $info = $telegram->getWebhookInformation();
        return response()->json($info);
    }

    /**
     * @param SetWebhook $request
     * @param BotInstance $botInstance
     * @return \Illuminate\Http\JsonResponse
     * @throws \ErrorException
     */
    public function update (SetWebhook $request, BotInstance $botInstance)
    {
        $url = $request->input('webhook');
        $conf = [
            'url' => $url . '/' . env('TELEGRAM_WEBHOOK_PREFIX')
        ];
        $cert = env('TELEGRAM_CERTIFICATE_PATH', false);
        if ($cert) {
            $conf['certificate'] = $cert;
        }

        try {
            $telegram = $botInstance->getBot('telegram');
            $response = $telegram->setWebhook($conf);
        } catch (TelegramSDKException $e) {
            return response()->json(['message' => $e->getMessage() . '('.$conf['url'].')'], 500);
        }
        return response()->json(['status' => $response]);
    }

    /**
     * @param BotInstance $botInstance
     * @return \Illuminate\Http\JsonResponse
     * @throws \ErrorException
     */
    public function destroy (BotInstance $botInstance)
    {
        return response()->json(['status' => $botInstance->getBot('telegram')->removeWebhook()]);
    }

    /**
     * set default webhook in order to the Telegrams configuration file
     * @param BotInstance $botInstance
     * @return \Illuminate\Http\JsonResponse
     * @throws \ErrorException
     */
    public function store (BotInstance $botInstance)
    {
        $conf = [
            'url' => env('TELEGRAM_WEBHOOK_URL')
        ];
        $cert = env('TELEGRAM_CERTIFICATE_PATH', false);
        if ($cert) {
            $conf['certificate'] = $cert;
        }
        try {
            $telegram = $botInstance->getBot('telegram');
            $response = $telegram->setWebhook($conf);
        } catch (TelegramSDKException $e) {
            return response()->json(['message' => $e->getMessage() . '('.$conf['url'].')'], 500);
        }
        return response()->json(['status' => $response]);
    }

}
