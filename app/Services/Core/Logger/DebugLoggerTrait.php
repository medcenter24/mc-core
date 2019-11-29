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
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Services\Core\Logger;


use Illuminate\Support\Facades\Log;

trait DebugLoggerTrait
{
    /**
     * @var bool
     */
    protected $debugMode = false;

    /**
     * @var string Channel
     */
    protected $channel;

    public function debugModeOn(): void
    {
        $this->debugMode = true;
    }

    public function debugModeOff(): void
    {
        $this->debugMode = false;
    }

    public function log(string $msg): void
    {
        if ($this->debugMode) {
            if ($this->channel) {
                Log::channel($this->channel)->debug($msg);
            } else {
                Log::debug($msg);
            }
        }
    }

    public function setLogChannel(string $channel): void
    {
        $this->channel = $channel;
    }
}
