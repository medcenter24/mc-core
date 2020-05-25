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
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Services\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\AccidentAbstract;
use medcenter24\mcCore\App\Entity\AccidentStatus;
use medcenter24\mcCore\App\Entity\City;
use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Events\AccidentStatusChangedEvent;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Entity\HospitalAccident;

class AccidentService extends AbstractModelService
{
    public const FIELD_ID = 'id';
    public const FIELD_PARENT_ID = 'parent_id';
    public const FIELD_PATIENT_ID = 'patient_id';
    public const FIELD_ACCIDENT_TYPE_ID = 'accident_type_id';
    public const FIELD_ACCIDENT_STATUS_ID = 'accident_status_id';
    public const FIELD_ASSISTANT_ID = 'assistant_id';
    public const FIELD_ASSISTANT_REF_NUM = 'assistant_ref_num';
    public const FIELD_ASSISTANT_INVOICE_ID = 'assistant_invoice_id';
    public const FIELD_ASSISTANT_GUARANTEE_ID = 'assistant_guarantee_id';
    public const FIELD_FORM_REPORT_ID = 'form_report_id';
    public const FIELD_CITY_ID = 'city_id';
    public const FIELD_CASEABLE_PAYMENT_ID = 'caseable_payment_id';
    public const FIELD_INCOME_PAYMENT_ID = 'income_payment_id';
    public const FIELD_ASSISTANT_PAYMENT_ID = 'assistant_payment_id';
    public const FIELD_CASEABLE_ID = 'caseable_id';
    public const FIELD_CASEABLE_TYPE = 'caseable_type';
    public const FIELD_REF_NUM = 'ref_num';
    public const FIELD_TITLE = 'title';
    public const FIELD_ADDRESS = 'address';
    public const FIELD_HANDLING_TIME = 'handling_time';
    public const FIELD_CONTACTS = 'contacts';
    public const FIELD_SYMPTOMS = 'symptoms';
    public const FIELD_CREATED_BY = 'created_by';
    public const FIELD_CLOSED_AT = 'closed_at';

    public const FILLABLE = [
        self::FIELD_PARENT_ID,
        self::FIELD_PATIENT_ID,
        self::FIELD_ACCIDENT_TYPE_ID,
        self::FIELD_ACCIDENT_STATUS_ID,
        self::FIELD_ASSISTANT_ID,
        self::FIELD_ASSISTANT_REF_NUM,
        self::FIELD_ASSISTANT_INVOICE_ID,
        self::FIELD_ASSISTANT_GUARANTEE_ID,
        self::FIELD_FORM_REPORT_ID,
        self::FIELD_CITY_ID,
        self::FIELD_CASEABLE_PAYMENT_ID,
        self::FIELD_INCOME_PAYMENT_ID,
        self::FIELD_ASSISTANT_PAYMENT_ID,
        self::FIELD_CASEABLE_ID,
        self::FIELD_CASEABLE_TYPE,
        self::FIELD_REF_NUM,
        self::FIELD_TITLE,
        self::FIELD_ADDRESS,
        self::FIELD_HANDLING_TIME,
        self::FIELD_CONTACTS,
        self::FIELD_SYMPTOMS,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_PARENT_ID,
        self::FIELD_PATIENT_ID,
        self::FIELD_ACCIDENT_TYPE_ID,
        self::FIELD_ACCIDENT_STATUS_ID,
        self::FIELD_ASSISTANT_ID,
        self::FIELD_ASSISTANT_REF_NUM,
        self::FIELD_ASSISTANT_INVOICE_ID,
        self::FIELD_ASSISTANT_GUARANTEE_ID,
        self::FIELD_REF_NUM,
        self::FIELD_TITLE,
        self::FIELD_CITY_ID,
        self::FIELD_ADDRESS,
        self::FIELD_CONTACTS,
        self::FIELD_SYMPTOMS,
        self::FIELD_HANDLING_TIME,
        self::FIELD_FORM_REPORT_ID,
        self::FIELD_CLOSED_AT,
    ];

    public const UPDATABLE = [
        self::FIELD_PARENT_ID,
        self::FIELD_PATIENT_ID,
        self::FIELD_ACCIDENT_TYPE_ID,
        self::FIELD_ACCIDENT_STATUS_ID,
        self::FIELD_ASSISTANT_ID,
        self::FIELD_ASSISTANT_REF_NUM,
        self::FIELD_ASSISTANT_INVOICE_ID,
        self::FIELD_ASSISTANT_GUARANTEE_ID,
        self::FIELD_FORM_REPORT_ID,
        self::FIELD_CITY_ID,
        self::FIELD_CASEABLE_PAYMENT_ID,
        self::FIELD_INCOME_PAYMENT_ID,
        self::FIELD_ASSISTANT_PAYMENT_ID,
        self::FIELD_REF_NUM,
        self::FIELD_TITLE,
        self::FIELD_ADDRESS,
        self::FIELD_HANDLING_TIME,
        self::FIELD_CONTACTS,
        self::FIELD_SYMPTOMS,
    ];

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
            self::FIELD_REF_NUM => $ref
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
            self::FIELD_ASSISTANT_REF_NUM => $ref
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
        $accident = $this->first([self::FIELD_REF_NUM => $ref]);
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
        return Accident::where(self::FIELD_CREATED_AT, '>=', $fromDate)
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
        return Accident::orderBy(self::FIELD_CREATED_AT, 'desc');
    }

    /**
     * @param Accident $accident
     * @return City|mixed
     */
    public function getCity(Accident $accident)
    {
        return $accident->getAttribute(self::FIELD_CITY_ID) ?: new City();
    }

    /**
     * @param Accident $accident
     * @return Collection
     */
    public function getAccidentServices(Accident $accident): Collection
    {
        $services = null;
        if ($accident->isDoctorCaseable()) {
            $caseable = $accident->getAttribute('caseable');
            $services = $caseable->getAttribute('services');
        }
        return $services ?: collect([]);
    }

    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_HANDLING_TIME => null,
            self::FIELD_ASSISTANT_REF_NUM => '',
            self::FIELD_CONTACTS => '',
            self::FIELD_SYMPTOMS => '',
            self::FIELD_CREATED_BY => 0,
            self::FIELD_PARENT_ID => 0,
            self::FIELD_PATIENT_ID => 0,
            self::FIELD_ACCIDENT_TYPE_ID => 0,
            self::FIELD_ACCIDENT_STATUS_ID => 0,
            self::FIELD_ASSISTANT_ID => 0,
            self::FIELD_ASSISTANT_INVOICE_ID => 0,
            self::FIELD_ASSISTANT_GUARANTEE_ID => 0,
            self::FIELD_CITY_ID => 0,
            self::FIELD_REF_NUM => '',
            self::FIELD_TITLE => '',
            self::FIELD_ADDRESS => '',
            self::FIELD_FORM_REPORT_ID => 0,
            self::FIELD_CASEABLE_PAYMENT_ID => 0,
            self::FIELD_INCOME_PAYMENT_ID => 0,
            self::FIELD_ASSISTANT_PAYMENT_ID => 0,
            self::FIELD_CASEABLE_TYPE => DoctorAccident::class,
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
            === $this->getServiceLocator()->get(AccidentStatusService::class)->getClosedStatus()->getAttribute('id');
    }

    /**
     * Set new status to accident
     * @param AccidentAbstract $accident
     * @param AccidentStatus $status
     * @param string $comment
     */
    public function setStatus(
        AccidentAbstract $accident,
        AccidentStatus $status,
        string $comment = ''): void
    {
        // do not prevent changing on closed accident because it nonsense
        // if we need to prevent then it needs to be in the controller not the service

        // I need to prevent all the times when I'm changing the status
        $accident->runStatusUpdating();
        $accident->update([self::FIELD_ACCIDENT_STATUS_ID => $status->getAttribute('id')]);
        $accident->refresh();

        Log::debug('Set new status to accident', [
            self::FIELD_ACCIDENT_STATUS_ID => $status->getAttribute('id'),
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
     */
    private function setStatusByEvents(Model $model, Accident $accident, array $events, $comment = ''): void
    {
        if ($model->isDirty()) {
            $dirty = array_keys($model->getDirty());
            foreach ($dirty as $key) {
                if (array_key_exists($key, $events) && $model->$key) {
                    /** @var AccidentStatus $status */
                    $status = $this->getAccidentStatusesService()->firstOrCreate([
                        AccidentStatusService::FIELD_TITLE => $events[$key]['status'],
                        AccidentStatusService::FIELD_TYPE => $events[$key]['type'],
                    ]);
                    $this->setStatus($accident, $status);
                }
            }
        }
    }

    /**
     * @return AccidentStatusService
     */
    protected function getAccidentStatusesService(): AccidentStatusService
    {
        return $this->getServiceLocator()->get(AccidentStatusService::class);
    }

    /**
     * @param Accident $accident
     * @param string $comment
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
     */
    public function updateHospitalAccidentStatus(HospitalAccident $hospitalAccident, $comment = 'Hospital accident changed'): void
    {
        if (!$hospitalAccident->getAttribute('accident')) {
            return; // don't do status changing when we don't have an accident
        }

        // changing of the fields in `keys` will provide status from the `value`
        $events = [
            'hospital_id' => [
                'status' => AccidentStatusService::STATUS_ASSIGNED,
                'type' => AccidentStatusService::TYPE_HOSPITAL,
            ],
            'hospital_guarantee_id' => [
                'status' => AccidentStatusService::STATUS_HOSPITAL_GUARANTEE,
                'type' => AccidentStatusService::TYPE_HOSPITAL,
            ],
            'hospital_invoice_id' => [
                'status' => AccidentStatusService::STATUS_HOSPITAL_INVOICE,
                'type' => AccidentStatusService::TYPE_HOSPITAL,
            ],
        ];

        $this->setStatusByEvents($hospitalAccident, $hospitalAccident->getAttribute('accident'), $events, $comment);
    }

    /**
     * When the DoctorAccident's property updated
     * @param DoctorAccident $doctorAccident
     * @param string $comment
     */
    public function updateDoctorAccidentStatus(DoctorAccident $doctorAccident, $comment = 'Doctor accident changed'): void
    {
        if (!$doctorAccident->getAttribute('accident')) {
            return; // don't do status changing when we don't have an accident
        }

        // changing of the fields in `keys` will provide status from the `value`
        $events = [
            'doctor_id' => [
                'status' => AccidentStatusService::STATUS_ASSIGNED,
                'type' => AccidentStatusService::TYPE_DOCTOR,
            ],
        ];

        $this->setStatusByEvents($doctorAccident, $doctorAccident->getAttribute('accident'), $events, $comment);
    }

    /**
     * When the accidents property updated
     * @param Accident $accident
     * @param string $comment
     */
    public function updateAccidentStatus(Accident $accident, $comment = 'Accident updated'): void
    {
        if ($accident->isStatusUpdatingRun()) {
            $accident->stopStatusUpdating();
            return; // skip saving of the status
        }
        $events = [
            self::FIELD_ID => [
                'status' => AccidentStatusService::STATUS_NEW,
                'type' => AccidentStatusService::TYPE_ACCIDENT,
            ],
            self::FIELD_ASSISTANT_INVOICE_ID => [
                'status' => AccidentStatusService::STATUS_ASSISTANT_INVOICE,
                'type' => AccidentStatusService::TYPE_ASSISTANT,
            ],
            self::FIELD_ASSISTANT_GUARANTEE_ID => [
                'status' => AccidentStatusService::STATUS_ASSISTANT_GUARANTEE,
                'type' => AccidentStatusService::TYPE_ASSISTANT,
            ],
            self::FIELD_ASSISTANT_PAYMENT_ID => [
                'status' => AccidentStatusService::STATUS_PAID,
                'type' => AccidentStatusService::TYPE_ASSISTANT,
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
