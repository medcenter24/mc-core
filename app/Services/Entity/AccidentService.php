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
use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Events\Accident\Caseable\AccidentUpdatedEvent;
use medcenter24\mcCore\App\Events\Accident\Status\AccidentStatusChangedEvent;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;

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
    // for the calculation and statistic payment could be changed
    // and will be not the same as invoice payment is
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

    // invoice contains the real value of payment
    public const RELATION_ASSISTANT_INVOICE = 'assistantInvoice';

    public const CASEABLE_TYPE_HOSPITAL = HospitalAccident::class;
    public const CASEABLE_TYPE_DOCTOR = DoctorAccident::class;

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

    public const DATE_FIELDS = [
        AccidentService::FIELD_CREATED_AT,
        AccidentService::FIELD_DELETED_AT,
        AccidentService::FIELD_UPDATED_AT,
        AccidentService::FIELD_HANDLING_TIME,
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
     * Get all accidents, assigned to the assistance
     * @param $assistanceId
     * @param $fromDate
     * @return int
     */
    public function getCountByAssistance($assistanceId, $fromDate): int
    {
        return $this->getQuery([self::FIELD_ASSISTANT_ID => $assistanceId])
            ->where(self::FIELD_CREATED_AT, '>=', $fromDate)
            ->count();
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
        /** @var AccidentStatus $currentStatus */
        $currentStatus = $accident->getAttribute('accidentStatus');
        /** @var AccidentStatus $closedStatus */
        $closedStatus = $this->getAccidentStatusesService()->getClosedStatus();
        return $currentStatus && $currentStatus->getAttribute('id') === $closedStatus->getAttribute('id');
    }

    /**
     * Set new status to accident
     * @param AccidentAbstract $accident
     * @param AccidentStatus $status
     * @param string $comment
     * @fires AccidentStatusChangedEvent
     * @throws InconsistentDataException
     */
    public function setStatus(
        AccidentAbstract $accident,
        AccidentStatus $status,
        string $comment = ''): void
    {
        $this->findAndUpdate([
            self::FIELD_ID,
        ], [
            self::FIELD_ID => $accident->getAttribute(self::FIELD_ID),
            self::FIELD_ACCIDENT_STATUS_ID => $status->getAttribute('id'),
        ]);
        // apply changes to loaded model
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
     * @return AccidentStatusService
     */
    protected function getAccidentStatusesService(): AccidentStatusService
    {
        return $this->getServiceLocator()->get(AccidentStatusService::class);
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
        $status = $this->getAccidentStatusesService()->getDoctorInProgressStatus();
        if ($accidentStatus->getAttribute('id') !== $status->getAttribute('id')) {
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
     * @param Accident $accident
     * @param string $comment
     * @throws InconsistentDataException
     */
    public function closeAccident(Accident $accident, $comment = 'closed'): void
    {
        $this->setStatus($accident, $this->getAccidentStatusesService()->getClosedStatus(), $comment);
    }

    /**
     * @param array $data
     * @return Model
     */
    public function create(array $data = []): Model
    {
        /** @var Accident $accident */
        $accident = parent::create($data);
        event(new AccidentUpdatedEvent($accident));
        $accident->refresh();
        return $accident;
    }

    /**
     * @param array $filterByFields
     * @param array $data
     * @return Model
     * @throws InconsistentDataException
     */
    public function findAndUpdate(array $filterByFields, array $data): Model
    {
        /** @var Accident $previousAccident */
        $previousAccident = $this->first(
            $this->convertToFilter($filterByFields, $data)
        );

        if ($this->isClosed($previousAccident)) {
            throw new InconsistentDataException('Accident closed and can not be changed', 422);
        }

        /** @var Accident $accident */
        $accident = parent::findAndUpdate($filterByFields, $data);
        event(new AccidentUpdatedEvent($accident, $previousAccident));
        return $accident;
    }

    public function isDoctorAccident(Accident $accident): bool
    {
        return $accident->getAttribute(self::FIELD_CASEABLE_TYPE) === DoctorAccident::class;
    }

    public function isHospitalAccident(Accident $accident): bool
    {
        return $accident->getAttribute(self::FIELD_CASEABLE_TYPE) === HospitalAccident::class;
    }
}
