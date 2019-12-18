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

namespace medcenter24\mcCore\App\Services;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\AccidentStatus;
use medcenter24\mcCore\App\City;
use Illuminate\Support\Collection;
use medcenter24\mcCore\App\DoctorAccident;
use medcenter24\mcCore\App\Events\AccidentStatusChangedEvent;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\HospitalAccident;

class AccidentService extends AbstractModelService
{
    public function getClassName(): string
    {
        return Accident::class;
    }

    /**
     * Calculate Accidents by the referral number
     * @param string $ref
     * @return int
     */
    public function getCountByReferralNum (string $ref = ''): int
    {
        return $this->count([
            'ref_num' => $ref
        ]);
    }

    /**
     * @param string $ref
     * @return Accident|null
     */
    public function getByAssistantRefNum(string $ref): ?Accident
    {
        /** @var Accident $accident */
        $accident = $this->first([
            'assistant_ref_num' => $ref
        ]);
        return $accident;
    }

    /**
     * @param string $ref
     * @return Accident|null
     */
    public function getByRefNum (string $ref = ''): ?Accident
    {
        /** @var Accident $accident */
        $accident = $this->first(['ref_num' => $ref]);
        return $accident;
    }

    /**
     * Get all accidents, assigned to the assistance
     * @param $assistanceId
     * @param $fromDate
     * @return int
     */
    public function getCountByAssistance($assistanceId, $fromDate): int
    {
        return Accident::where('created_at', '>=', $fromDate)
            ->where('assistant_id', '=', $assistanceId)
            ->count();
    }

    /**
     * @param array $filters
     * @return mixed
     * @deprecated just don't do this
     */
    public function getCasesQuery(array $filters = [])
    {
        return Accident::orderBy('created_at', 'desc');
    }

    /**
     * @param Accident $accident
     * @return City|mixed
     */
    public function getCity(Accident $accident)
    {
        return $accident->getAttribute('city_id') ?: new City();
    }

    /**
     * @param Accident $accident
     * @return Collection
     */
    public function getAccidentServices(Accident $accident): Collection
    {
        $accidentServices = $accident->services;
        if ($accident->caseable) {
            $accidentServices = $accidentServices->merge($accident->caseable->services);
        }
        return $accidentServices ?: collect([]);
    }

    protected function getRequiredFields(): array
    {
        return [
            'handling_time' => null,
            'assistant_ref_num' => '',
            'contacts' => '',
            'symptoms' => '',
            'created_by' => 0,
            'parent_id' => 0,
            'patient_id' => 0,
            'accident_type_id' => 0,
            'accident_status_id' => 0,
            'assistant_id' => 0,
            'assistant_invoice_id' => 0,
            'assistant_guarantee_id' => 0,
            'city_id' => 0,
            'ref_num' => '',
            'title' => '',
            'address' => '',
            'form_report_id' => 0,
            'caseable_payment_id' => 0,
            'income_payment_id' => 0,
            'assistant_payment_id' => 0,
        ];
    }

    /**
     * Check if the accident was closed
     * @param Accident $accident
     * @return bool
     */
    public function isClosed(Accident $accident): bool
    {
        return $accident->getAttribute('accidentStatus')->getAttribute('id')
            === $this->getServiceLocator()->get(AccidentStatusesService::class)->getClosedStatus()->getAttribute('id');
    }

    /**
     * Set new status to accident
     * @param Accident $accident
     * @param AccidentStatus $status
     * @param string $comment
     * @throws InconsistentDataException
     */
    public function setStatus(Accident $accident, AccidentStatus $status, $comment = ''): void
    {
        // do not prevent changing on closed accident because it nonsense
        // if we need to prevent then it needs to be in the controller not the service

        // I need to prevent all the times when I'm changing the status
        $accident->runStatusUpdating();
        $accident->update(['accident_status_id' => $status->getAttribute('id')]);
        $accident->refresh();

        Log::debug('Set new status to accident', [
            'status_id' => $status->getAttribute('id'),
            'status_title' => $status->getAttribute('title'),
            'status_type' => $status->getAttribute('type'),
            'accident_id' => $accident->getAttribute('id'),
        ]);
        event(new AccidentStatusChangedEvent($accident, $comment));
    }

    /**
     * @param Model $model
     * @param Accident $accident
     * @param array $events
     * @param string $comment
     * @throws InconsistentDataException
     */
    private function setStatusByEvents(Model $model, Accident $accident, array $events, $comment = ''): void
    {
        if ($model->isDirty()) {
            $dirty = array_keys($model->getDirty());
            foreach ($dirty as $key) {
                if (array_key_exists($key, $events) && $model->$key) {
                    /** @var AccidentStatus $status */
                    $status = $this->getServiceLocator()->get(AccidentStatusesService::class)->firstOrCreate([
                        'title' => $events[$key]['status'],
                        'type' => $events[$key]['type'],
                    ]);
                    $this->setStatus($accident, $status);
                }
            }
        }
    }

    /**
     * @return AccidentStatusesService
     */
    protected function getAccidentStatusesService(): AccidentStatusesService
    {
        return $this->getServiceLocator()->get(AccidentStatusesService::class);
    }

    /**
     * @param Accident $accident
     * @param string $comment
     * @throws InconsistentDataException
     */
    public function moveDoctorAccidentToInProgressState(Accident $accident, $comment = 'moved'): void
    {
        /** @var AccidentStatus $accidentStatus */
        $accidentStatus = $accident->getAttribute('accidentStatus');

        /** @var AccidentStatus $status */
        $status = $this->getAccidentStatusesService()->getDoctorAssignedStatus();
        if ($accidentStatus->getAttribute('id') === $status->getAttribute('id')) {
            /** @var AccidentStatus $status */
            $status = $this->getAccidentStatusesService()->getDoctorInProgressStatus();
            $this->setStatus($accident, $status, $comment);
        }
    }

    /**
     * @param $accident
     * @param string $comment
     * @throws InconsistentDataException
     */
    public function rejectDoctorAccident($accident, $comment = 'rejected'): void
    {
        $status = $this->getAccidentStatusesService()->getDoctorRejectedStatus();
        $this->setStatus($accident, $status, $comment);
    }

    /**
     * When the HospitalAccident's property updated
     * @param HospitalAccident $hospitalAccident
     * @param string $comment
     * @throws InconsistentDataException
     */
    public function updateHospitalAccidentStatus(HospitalAccident $hospitalAccident, $comment = 'Hospital accident changed'): void
    {
        if (!$hospitalAccident->getAttribute('accident')) {
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

        $this->setStatusByEvents($hospitalAccident, $hospitalAccident->getAttribute('accident'), $events, $comment);
    }

    /**
     * When the DoctorAccident's property updated
     * @param DoctorAccident $doctorAccident
     * @param string $comment
     * @throws InconsistentDataException
     */
    public function updateDoctorAccidentStatus(DoctorAccident $doctorAccident, $comment = 'Doctor accident changed'): void
    {
        if (!$doctorAccident->getAttribute('accident')) {
            return; // don't do status changing when we don't have an accident
        }

        // changing of the fields in `keys` will provide status from the `value`
        $events = [
            'doctor_id' => [
                'status' => AccidentStatusesService::STATUS_ASSIGNED,
                'type' => AccidentStatusesService::TYPE_DOCTOR,
            ],
        ];

        $this->setStatusByEvents($doctorAccident, $doctorAccident->getAttribute('accident'), $events, $comment);
    }

    /**
     * When the accidents property updated
     * @param Accident $accident
     * @param string $comment
     * @throws InconsistentDataException
     */
    public function updateAccidentStatus(Accident $accident, $comment = 'Accident updated'): void
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
     * @param Accident $accident
     * @param string $comment
     * @throws InconsistentDataException
     */
    public function closeAccident(Accident $accident, $comment = 'closed'): void
    {
        $this->setStatus($accident, $this->getAccidentStatusesService()->getClosedStatus(), $comment);
    }
}
