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

namespace medcenter24\mcCore\App\Listeners;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Events\Accident\Caseable\DoctorAccidentUpdatedEvent;
use Telegram;
use Telegram\Bot\Keyboard\Button;
use Telegram\Bot\Keyboard\Keyboard;
use Throwable;

class SendTelegramMessageOnDocAssignment
{

    /**
     * @param DoctorAccidentUpdatedEvent $event
     * @throws Throwable
     */
    public function handle(DoctorAccidentUpdatedEvent $event): void
    {
        $prev = $event->getPreviousDoctorAccident();
        $doctorAccident = $event->getDoctorAccident();

        Log::info('Run Telegram message sender on event', [
            'doc' => $doctorAccident->getAttribute('doctor_id'),
            'previousDoctorAccidentDoctor' => $prev ? $prev->getAttribute('doctor_id') : '',
            'accident_ref_num' => $doctorAccident->getAttribute('accident')->getAttribute('ref_num'),
            'accident' => $doctorAccident->getAttribute('accident')->getAttribute('id'),
            'doctorAccidentId' => $doctorAccident->getAttribute('id'),
        ]);

        if (
            $doctorAccident->doctor_id
            && (!$prev || (int)$prev->doctor_id !== (int)$doctorAccident->doctor_id)
            && $doctorAccident->doctor->user_id
            && $doctorAccident->doctor->user->telegram
        ) {
            $doctorUser = $doctorAccident->doctor->user;
            App::setLocale($doctorUser->lang);
            $keyboard = new Keyboard([
                'keyboard' => [
                    [
                        new Button([
                            'text' => trans('content.pickup_case', ['ref_num' => $doctorAccident->accident->ref_num]),
                            // 'request_location' => true, not implemented yet (on telegrams side)
                        ])
                    ]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
            ]);

            Telegram::sendMessage([
                'chat_id' => $doctorAccident->doctor->user->telegram->telegram_id,
                'text' => view('case.info', compact('doctorAccident'))->render(),
                'parse_mode' => 'HTML',
                'reply_markup' => $keyboard,
            ]);
        } elseif (!$doctorAccident->doctor) {
            Log::warning('Doctor will not receive any messages to the telegram, because we do not have a doctor', [
                'doctorAccidentId' => $doctorAccident->id,
            ]);
        } elseif(!$doctorAccident->doctor->user_id) {
            Log::warning('Doctor will not receive any messages to the telegram, because he does not have an assignment to a system user', [
                'doctorAccidentId' => $doctorAccident->id,
                'userId' => $doctorAccident->doctor->user_id,
            ]);
        } elseif(!$doctorAccident->doctor->user->telegram) {
            Log::warning('Doctor will not receive any messages to the telegram, because he does not have an assignment to telegram', [
                'doctorAccidentId' => $doctorAccident->id,
                'userId' => $doctorAccident->doctor->user_id,
                'telegram' => $doctorAccident->doctor->user->telegram,
            ]);
        }
    }
}
