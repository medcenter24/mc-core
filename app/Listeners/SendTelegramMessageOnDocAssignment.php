<?php

namespace App\Listeners;

use App\Events\DoctorAccidentUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
            \Telegram::sendMessage([
                'chat_id' => $doctorAccident->doctor->user->telegram->telegram_id,
                'text' => view('case.info', compact('doctorAccident'))->render(),
                'parse_mode' => 'HTML',
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
