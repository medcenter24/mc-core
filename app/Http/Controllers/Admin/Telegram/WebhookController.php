<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Admin\Telegram;


use App\Http\Controllers\AdminController;
use App\Http\Requests\Telegram\SetWebhook;
use Telegram;
use Telegram\Bot\Exceptions\TelegramSDKException;

class WebhookController extends  AdminController
{

    public function index ()
    {
        $info = Telegram::getWebhookInfo();

        return response()->json([
            'webhookUrl' => $info->getUrl(),
            'hasCertificate' => $info->getHasCustomCertificate(),
            'pendingUpdateCount' => $info->getPendingUpdateCount(),
            'lastErrorDate' => $info->getLasErrorDate(),
            'maxConnections' => $info->getMaxConnections(),
            'allowedUpdates' => $info->getAllowedUpdates(),
        ]);
    }

    public function update (SetWebhook $request)
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
            $response = Telegram::setWebhook($conf);
        } catch (TelegramSDKException $e) {
            return response()->json(['message' => $e->getMessage() . '('.$conf['url'].')'], 500);
        }
        return response()->json(['status' => $response]);
    }

    public function destroy ()
    {
        $response = Telegram::removeWebhook();
        return response()->json(['status' => $response]);
    }

    /**
     * set default webhook in order to the configuration file
     */
    public function store ()
    {
        $conf = [
            'url' => env('TELEGRAM_WEBHOOK_URL')
        ];
        $cert = env('TELEGRAM_CERTIFICATE_PATH', false);
        if ($cert) {
            $conf['certificate'] = $cert;
        }
        try {
            $response = Telegram::setWebhook($conf);
        } catch (TelegramSDKException $e) {
            return response()->json(['message' => $e->getMessage() . '('.$conf['url'].')'], 500);
        }
        return response()->json(['status' => $response]);
    }

}
