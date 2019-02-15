<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Listeners;

use App\Events\DoctorAccidentUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Telegram\Bot\Keyboard\Button;
use Telegram\Bot\Keyboard\Keyboard;

class SendTelegramMessageOnDocAssignment
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * @param DoctorAccidentUpdatedEvent $event
     * @throws \Throwable
     */
    public function handle(DoctorAccidentUpdatedEvent $event)
    {
        $prev = $event->getPreviousData();
        $doctorAccident = $event->getDoctorAccident();

        if (
            $doctorAccident->doctor_id &&
            (!$prev || $prev->doctor_id != $doctorAccident->doctor_id)
            && $doctorAccident->doctor->user_id
            && $doctorAccident->doctor->user->telegram
        ) {
            $doctorUser = $doctorAccident->doctor->user;
            \App::setLocale($doctorUser->lang);
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

            \Telegram::sendMessage([
                'chat_id' => $doctorAccident->doctor->user->telegram->telegram_id,
                'text' => view('case.info', compact('doctorAccident'))->render(),
                'parse_mode' => 'HTML',
                'reply_markup' => $keyboard,
            ]);
        } elseif (!$doctorAccident->doctor) {
            \Log::warning('Doctor will not receive any messages to the telegram, because we do not have doctor', [
                'doctorAccidentId' => $doctorAccident->id,
            ]);
        } elseif(!$doctorAccident->doctor->user_id) {
            \Log::warning('Doctor will not receive any messages to the telegram, because he does not have assignment to user', [
                'doctorAccidentId' => $doctorAccident->id,
                'userId' => $doctorAccident->doctor->user_id,
            ]);
        } elseif(!$doctorAccident->doctor->user->telegram) {
            \Log::warning('Doctor will not receive any messages to the telegram, because he does not have assignment to telegram', [
                'doctorAccidentId' => $doctorAccident->id,
                'userId' => $doctorAccident->doctor->user_id,
                'telegram' => $doctorAccident->doctor->user->telegram,
            ]);
        }
    }
}
