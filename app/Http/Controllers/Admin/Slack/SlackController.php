<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Http\Controllers\Admin\Slack;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use medcenter24\mcCore\App\Http\Controllers\AdminController;

class SlackController extends AdminController
{
    public function info(): JsonResponse
    {
        $webhook = $this->getConfiguredWebhook();
        $info = [
            'initialized' => $webhook !== '',
            'level' => $this->getLevel(),
        ];
        return response()->json($info);
    }

    /**
     * @return string
     */
    private function getConfiguredWebhook(): string
    {
        return env('LOG_SLACK_WEBHOOK_URL', '');
    }

    private function getLevel(): string
    {
        return env('LOG_SLACK_LEVEL', 'critical');
    }

    public function log(Request $request): JsonResponse
    {
        $type = $request->input('type', 'error');

        switch ($type) {
            case 'critical':
                Log::critical($type . date(' d.m.Y H:i:s'));
                break;
            case 'error':
                Log::error($type . date(' d.m.Y H:i:s'));
                break;
            case 'warning':
                Log::warning($type . date(' d.m.Y H:i:s'));
                break;
            case 'info':
                Log::info($type . date('d .m.Y H:i:s'));
                break;
            case 'debug':
                Log::debug($type . date(' d.m.Y H:i:s'));
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $type . ' Sent',
        ]);
    }
}
