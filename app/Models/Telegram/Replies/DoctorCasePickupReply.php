<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Telegram\Replies;


use App\Accident;
use App\AccidentStatus;
use App\Services\AccidentStatusesService;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

/**
 * When doctor press reply button on assignment
 *
 * Class DoctorCasePickup
 * @package App\Models\Telegram\Replies
 */
class DoctorCasePickupReply
{

    /**
     * @var Message
     */
    private $message;

    /**
     * @var null|Update
     */
    private $update;

    /**
     * @var AccidentStatusesService
     */
    private $accidentStatusesService;

    /**
     * DoctorCasePickupReply constructor.
     * @param Update $update
     * @param AccidentStatusesService $accidentStatusesService
     * @throws \App\Exceptions\InconsistentDataException
     */
    public function __construct(Update $update, AccidentStatusesService $accidentStatusesService)
    {
        $this->update = $update;
        $this->message = $this->update->getMessage();
        $this->accidentStatusesService = $accidentStatusesService;
        \App::setLocale($this->message->from->languageCode);
        $this->acceptCase();
    }

    /**
     * Doing all needed things after receive confirmation
     * @throws \App\Exceptions\InconsistentDataException
     */
    public function acceptCase()
    {
        $accident = $this->lookingForAccident();
        if ($accident instanceof Accident && $accident->id) {

            $status = AccidentStatus::where('title', AccidentStatusesService::STATUS_IN_PROGRESS)
                ->where('type', AccidentStatusesService::TYPE_DOCTOR)->first();

            if (!$status || !$status->id) {
                \Log::info('Cant find status in progress for the doctor');
            } else {
                $this->accidentStatusesService->set($accident, $status, 'Telegram Accept button after assignment');
            }
        }
    }

    public function lookingForAccident()
    {
        $body = $this->message->text;
        $refNumber = false;
        if (preg_match('/([^-\s]+-\d{6}-[^-\s]+)/', $body, $match)) {
            $refNumber = $match[1];
        }

        $accident = false;
        if ($refNumber) {
            $accident = Accident::where('ref_num', $refNumber)->first();
        }

        return $accident;
    }

}