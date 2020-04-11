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

namespace medcenter24\mcCore\App\Models\Telegram\Replies;


use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\AccidentStatus;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

/**
 * When doctor press reply button on assignment
 *
 * Class DoctorCasePickup
 * @package medcenter24\mcCore\App\Models\Telegram\Replies
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
     * DoctorCasePickupReply constructor.
     * @param Update $update
     * @param AccidentStatusService $accidentStatusesService
     * @throws \medcenter24\mcCore\App\Exceptions\InconsistentDataException
     */
    public function __construct(Update $update)
    {
        $this->update = $update;
        $this->message = $this->update->getMessage();
        \App::setLocale($this->message->from->languageCode);
        $this->acceptCase();
    }

    /**
     * Doing all needed things after receive confirmation
     * @throws InconsistentDataException
     */
    public function acceptCase(): void
    {
        $accident = $this->lookingForAccident();
        if ($accident instanceof Accident && $accident->id) {

            $status = AccidentStatus::where('title', AccidentStatusService::STATUS_IN_PROGRESS)
                ->where('type', AccidentStatusService::TYPE_DOCTOR)->first();

            if (!$status || !$status->id) {
                Log::info('Cant find status in progress for the doctor');
            } else {
                $this->accidentService->setStatus($accident, $status, 'Telegram Accept button after assignment');
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