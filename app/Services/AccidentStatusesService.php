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

namespace App\Services;


use App\Accident;
use App\AccidentStatus;
use App\DoctorAccident;
use App\Events\AccidentStatusChangedEvent;
use App\Exceptions\InconsistentDataException;
use App\HospitalAccident;
use Illuminate\Database\Eloquent\Model;

class AccidentStatusesService
{
    const TYPE_ACCIDENT = 'accident';
    const TYPE_DOCTOR = 'doctor';
    const TYPE_HOSPITAL = 'hospital';
    const TYPE_ASSISTANT = 'assistant';

    const STATUS_NEW = 'new';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_SENT = 'sent';
    const STATUS_PAID = 'paid';
    const STATUS_REJECT = 'reject';
    const STATUS_CLOSED = 'closed';

    const STATUS_HOSPITAL_GUARANTEE = 'hospital_guarantee';
    const STATUS_HOSPITAL_INVOICE = 'hospital_invoice';
    const STATUS_ASSISTANT_INVOICE = 'assistant_invoice';
    const STATUS_ASSISTANT_GUARANTEE = 'assistant_guarantee';

    /**
     * Set new status to accident
     * @param Accident $accident
     * @param AccidentStatus $status
     * @param string $comment
     */
    public function set(Accident $accident, AccidentStatus $status, $comment = '')
    {
        // I need to prevent all the times when I'm changing the status
        $accident->runStatusUpdating();
        $accident->accident_status_id = $status->id;
        $accident->save();
        $accident->refresh();

        \Log::debug('Set new status to accident', [
            'status_id' => $status->id,
            'status_title' => $status->title,
            'status_type' => $status->type,
            'accident_id' => $accident->id,
        ]);
        event(new AccidentStatusChangedEvent($accident, $comment));
    }

    /**
     * @param array $params
     * @throws InconsistentDataException
     * @return AccidentStatus
     */
    public function firstOrFail(array $params = [])
    {
        if (!count($params)) {
            throw new InconsistentDataException('Parameters should been provided');
        }

        return AccidentStatus::firstOrFail($params);
    }

    /**
     * When the accidents property updated
     * @param Accident $accident
     * @param string $comment
     */
    public function updateAccidentStatus(Accident $accident, $comment = 'Accident updated')
    {
        if ($accident->isStatusUpdatingRun()) {
            $accident->stopStatusUpdating();
            return; // skip saving of the status
        }
        $events = [
            'id' => [
                'status' => AccidentStatusesService::STATUS_NEW,
                'type' => AccidentStatusesService::TYPE_ACCIDENT,
            ],
            'assistant_invoice_id' => [
                'status' => AccidentStatusesService::STATUS_ASSISTANT_INVOICE,
                'type' => AccidentStatusesService::TYPE_ASSISTANT,
            ],
            'assistant_guarantee_id' => [
                'status' => AccidentStatusesService::STATUS_ASSISTANT_GUARANTEE,
                'type' => AccidentStatusesService::TYPE_ASSISTANT,
            ],
            'assistant_payment_id' => [
                'status' => AccidentStatusesService::STATUS_PAID,
                'type' => AccidentStatusesService::TYPE_ASSISTANT,
            ],
        ];

        $this->setStatusByEvents($accident, $accident, $events, $comment);
    }

    /**
     * @param Model $model
     * @param Accident $accident
     * @param array $events
     * @param string $comment
     */
    private function setStatusByEvents(Model $model, Accident $accident, array $events, $comment = '')
    {
        if ($model->isDirty()) {
            $dirty = array_keys($model->getDirty());
            foreach ($dirty as $key) {
                if (key_exists($key, $events) && $model->$key) {
                    $this->set($accident, AccidentStatus::firstOrCreate([
                        'title' => $events[$key]['status'],
                        'type' => $events[$key]['type'],
                    ]), $comment);
                }
            }
        }
    }

    /**
     * When the HospitalAccident's property updated
     * @param HospitalAccident $hospitalAccident
     * @param string $comment
     */
    public function updateHospitalAccidentStatus(HospitalAccident $hospitalAccident, $comment = 'Hospital accident changed')
    {
        if (!$hospitalAccident->accident) {
            return; // don't do status changing when we don't have an accident
        }

        // changing of the fields in `keys` will provide status from the `value`
        $events = [
            'hospital_id' => [
                'status' => AccidentStatusesService::STATUS_ASSIGNED,
                'type' => AccidentStatusesService::TYPE_HOSPITAL,
            ],
            'hospital_guarantee_id' => [
                'status' => AccidentStatusesService::STATUS_HOSPITAL_GUARANTEE,
                'type' => AccidentStatusesService::TYPE_HOSPITAL,
            ],
            'hospital_invoice_id' => [
                'status' => AccidentStatusesService::STATUS_HOSPITAL_INVOICE,
                'type' => AccidentStatusesService::TYPE_HOSPITAL,
            ],
        ];

        $this->setStatusByEvents($hospitalAccident, $hospitalAccident->accident, $events, $comment);
    }

    /**
     * When the DoctorAccident's property updated
     * @param DoctorAccident $doctorAccident
     * @param string $comment
     */
    public function updateDoctorAccidentStatus(DoctorAccident $doctorAccident, $comment = 'Doctor accident changed')
    {
        if (!$doctorAccident->accident) {
            return; // don't do status changing when we don't have an accident
        }

        // changing of the fields in `keys` will provide status from the `value`
        $events = [
            'doctor_id' => [
                'status' => AccidentStatusesService::STATUS_ASSIGNED,
                'type' => AccidentStatusesService::TYPE_DOCTOR,
            ],
        ];

        $this->setStatusByEvents($doctorAccident, $doctorAccident->accident, $events, $comment);
    }

    public function closeAccident(Accident $accident, $comment = 'closed')
    {
        $this->set($accident, AccidentStatus::firstOrCreate([
            'title' => AccidentStatusesService::STATUS_CLOSED,
            'type' => AccidentStatusesService::TYPE_ACCIDENT,
        ]), $comment);
    }

    public function moveDoctorAccidentToInProgressState(Accident $accident, $comment = 'moved')
    {
        $accidentStatus = $accident->accidentStatus;

        if ($accidentStatus->title == AccidentStatusesService::STATUS_ASSIGNED && $accidentStatus->type == AccidentStatusesService::TYPE_DOCTOR) {

            $status = AccidentStatus::firstOrCreate([
                'title' => AccidentStatusesService::STATUS_IN_PROGRESS,
                'type' => AccidentStatusesService::TYPE_DOCTOR,
            ]);

            $this->set($accident, $status, $comment);
        }
    }

    public function rejectDoctorAccident($accident, $comment = 'rejected')
    {
        $status = AccidentStatus::firstOrCreate([
            'title' => AccidentStatusesService::STATUS_REJECT,
            'type' => AccidentStatusesService::TYPE_DOCTOR,
        ]);

        $this->set($accident, $status, $comment);
    }

}
