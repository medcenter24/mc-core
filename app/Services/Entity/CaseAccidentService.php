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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Contract\General\Service\ModelService;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\AccidentAbstract;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Entity\Patient;
use medcenter24\mcCore\App\Events\Accident\Caseable\DoctorAccidentUpdatedEvent;
use medcenter24\mcCore\App\Events\Accident\Caseable\HospitalAccidentUpdatedEvent;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\ReferralNumberService;
use PDOException;

/**
 * To control case pages with aggregated cases data
 * i.e. patients, accidents, caseable, payments ... etc.
 * Class CaseAccidentService
 * @package medcenter24\mcCore\App\Services\Entity
 */
class CaseAccidentService implements ModelService
{
    use ServiceLocatorTrait;

    public const PROPERTY_ACCIDENT = 'accident';
    public const PROPERTY_DOCTOR_ACCIDENT = 'doctorAccident';
    public const PROPERTY_HOSPITAL_ACCIDENT = 'hospitalAccident';
    public const PROPERTY_SERVICES = 'services';
    public const PROPERTY_DIAGNOSTICS = 'diagnostics';
    public const PROPERTY_SURVEYS = 'surveys';
    public const PROPERTY_DOCUMENTS = 'documents';
    public const PROPERTY_CHECKPOINTS = 'checkpoints';
    public const PROPERTY_PATIENT = 'patient';
    private const PROPERTY_SERVICEABLES = 'serviceables';
    private const PROPERTY_SURVEABLES = 'surveables';
    private const PROPERTY_DIAGNOSTICABLES = 'diagnosticables';

    /**
     * @return Model|Accident
     */
    public function getModel(): Model|Accident
    {
        return new Accident();
    }

    /**
     * @param array $data
     * @return Model|Accident
     * @throws InconsistentDataException
     */
    public function create(array $data = []): Model|Accident
    {
        return $this->flushAccidentData($data);
    }

    /**
     * @param Accident $accident
     * @return Model|null|AccidentAbstract
     */
    private function getCaseable(Accident $accident): Model|Accident|null
    {
        $caseable = null;
        if ($this->getAccidentService()->isDoctorAccident($accident)) {
            $caseable = $this->getDoctorAccidentService()
                ->first([AbstractModelService::FIELD_ID => $accident->getAttribute(AccidentService::FIELD_CASEABLE_ID)]);
        } elseif ($this->getAccidentService()->isHospitalAccident($accident)) {
            $caseable = $this->getHospitalAccidentService()
                ->first([AbstractModelService::FIELD_ID => $accident->getAttribute(AccidentService::FIELD_CASEABLE_ID)]);
        }
        return $caseable;
    }

    /**
     * @param array $data
     * @param Accident|null $accident
     * @return Accident
     * @throws InconsistentDataException
     */
    private function flushAccidentData(array $data, Accident $accident = null): Accident
    {
        try {
            DB::beginTransaction();

            $previousCaseable = null;
            if ($accident) {
                $previousCaseable = $this->getCaseable($accident);
                $accident = $this->updateAccident($accident, $data);
            } else {
                $accident = $this->createAccident($data);
            }
            // throw exception if data not correct
            $this->validAccidentAccess($accident);

            // create/update caseable data
            $caseable = $this->flushCaseable($accident, $data);

            $this->updateCaseableMorphs($caseable, $data);

            /** @var Patient $patient */
            $patient = $this->getPatient($data);
            if ($patient && $patient->getKey() !== $accident->getAttribute(AccidentService::FIELD_PATIENT_ID)) {
                $accident->patient()->associate($patient);
                $accident->save();
            }

            // to generate correct ref num we do need to store everything before
            $this->storeRefNum($accident);

            // triggers an event
            // Have to update caseable here, because some important data could be changed
            // for example caseable could be touched before accident creation
            $this->emitCaseableEvent($data, $caseable, $previousCaseable);

            // only after we're sure that we have refNum record
            Log::info('created', ['ref' => $accident->getAttribute(AccidentService::FIELD_REF_NUM)]);

            $this->updateDocuments($accident, $data);
            $this->updateCheckpoints($accident, $data);

            DB::commit();
        } catch (PDOException $e) {
            DB::rollBack();
            throw new InconsistentDataException('This data can not been stored to the DB');
        }

        return $accident;
    }

    /**
     * @param Accident $accident
     * @param array $data
     * @return Accident|Model
     * @throws InconsistentDataException
     */
    private function updateAccident(Accident $accident, array $data): Accident
    {
        $accidentData = $this->getAccidentData($data);
        if (!array_key_exists(AccidentService::FIELD_ID, $accidentData)) {
            throw new InconsistentDataException('Accident identifier should be provided in the request data');
        }
        if ($accidentData[AccidentService::FIELD_ID] !== $accident->getKey()) {
            throw new InconsistentDataException('There are 2 different accidents in the request');
        }
        return $this->getAccidentService()->findAndUpdate(['id'], $accidentData);
    }

    /**
     * Validator
     * @param Accident $accident
     * @throws InconsistentDataException
     */
    private function validAccidentAccess(Accident $accident): void
    {
        // if it needed then status for this accident would be reset by the administrator
        if ($this->getAccidentService()->isClosed($accident)) {
            throw new InconsistentDataException('Already closed');
        }
    }

    /**
     * @param Accident $accident
     * @param array $data
     * @return AccidentAbstract|HospitalAccident|DoctorAccident
     * @throws InconsistentDataException
     */
    private function flushCaseable(
        Accident $accident,
        array $data
    ): AccidentAbstract|HospitalAccident|DoctorAccident {
        if (!$accident->getAttribute(AccidentService::FIELD_CASEABLE_ID)) {
            $caseable = $this->createCaseable($data);
            $accident->caseable()->associate($caseable)->save();
            $caseable->refresh();
        } else {
            $caseableId = (int) $accident->getAttribute(AccidentService::FIELD_CASEABLE_ID);
            $caseable = $this->updateCaseable($caseableId, $data);
        }

        return $caseable;
    }

    /**
     * @param Accident $accident
     * @param $data
     */
    private function updateDocuments(Accident $accident, $data): void
    {
        // I can provide list of documents to assign them to the accident directly
        $accident->documents()->detach();
        if (
            array_key_exists(self::PROPERTY_DOCUMENTS, $data)
            && is_array($data[self::PROPERTY_DOCUMENTS])
        ) {
            $accident->documents()->attach($data[self::PROPERTY_DOCUMENTS]);
        }
    }

    /**
     * @param Accident $accident
     * @param $data
     */
    private function updateCheckpoints(Accident $accident, $data): void
    {
        $accident->checkpoints()->detach();
        if (
            array_key_exists(self::PROPERTY_CHECKPOINTS, $data)
            && is_array($data[self::PROPERTY_CHECKPOINTS])
        ) {
            $accident->checkpoints()->attach($data[self::PROPERTY_CHECKPOINTS]);
        }
    }

    /**
     * @param Accident $accident
     */
    private function storeRefNum(Accident $accident): void
    {
        if (!$accident->getAttribute(AccidentService::FIELD_REF_NUM)) {
            $accident->setAttribute(
                AccidentService::FIELD_REF_NUM,
                $this->getReferralNumberService()->generate($accident)
            );
            $accident->save();
        }
    }

    /**
     * @return ReferralNumberService
     */
    private function getReferralNumberService(): ReferralNumberService
    {
        return $this->getServiceLocator()->get(ReferralNumberService::class);
    }

    /**
     * @return AccidentService
     */
    private function getAccidentService(): AccidentService
    {
        return $this->getServiceLocator()->get(AccidentService::class);
    }

    /**
     * @param array $data
     * @return array
     */
    private function getAccidentData(array $data): array
    {
        if (!array_key_exists(self::PROPERTY_ACCIDENT, $data)) {
            $data[self::PROPERTY_ACCIDENT] = [];
        }

        return $data[self::PROPERTY_ACCIDENT];
    }

    /**
     * @param $data
     * @return Accident|Model
     * @throws InconsistentDataException
     */
    private function createAccident(array $data): Accident
    {
        $accidentData = $this->getAccidentData($data);
        $accidentData[AccidentService::FIELD_CREATED_BY] = auth()->user() ? auth()->user()->getAuthIdentifier() : 0;
        return $this->getAccidentService()->create($accidentData);
    }

    /**
     * @param array $data
     * @return string
     */
    private function getCaseableType(array $data): string
    {
        $caseableType = DoctorAccident::class; // default
        if (array_key_exists(AccidentService::FIELD_CASEABLE_TYPE, $this->getAccidentData($data))) {
            $caseableType = $data[self::PROPERTY_ACCIDENT][AccidentService::FIELD_CASEABLE_TYPE];
        }
        return $caseableType;
    }

    /**
     * @return DoctorAccidentService
     */
    private function getDoctorAccidentService(): DoctorAccidentService
    {
        return $this->getServiceLocator()->get(DoctorAccidentService::class);
    }

    /**
     * @return HospitalAccidentService
     */
    private function getHospitalAccidentService(): HospitalAccidentService
    {
        return $this->getServiceLocator()->get(HospitalAccidentService::class);
    }

    /**
     * @param array $data
     * @return array
     */
    private function getDoctorAccidentData(array $data): array
    {
        return
            array_key_exists(self::PROPERTY_DOCTOR_ACCIDENT, $data)
            && is_array($data[self::PROPERTY_DOCTOR_ACCIDENT])
                ? $data[self::PROPERTY_DOCTOR_ACCIDENT]
                : [];
    }

    /**
     * @param array $data
     * @return array
     */
    private function getHospitalAccidentData(array $data): array
    {
        return
            array_key_exists(self::PROPERTY_HOSPITAL_ACCIDENT, $data)
            && is_array($data[self::PROPERTY_HOSPITAL_ACCIDENT])
                ? $data[self::PROPERTY_HOSPITAL_ACCIDENT]
                : [];
    }

    /**
     * @param array $data
     * @return Model|HospitalAccident|DoctorAccident
     * @throws InconsistentDataException
     */
    private function createCaseable(array $data): Model
    {
        if ( $this->isDoctorCase($data) ) {
            $caseable = $this->getDoctorAccidentService()->create(
                $this->getDoctorAccidentData($data)
            );
        } elseif ( $this->isHospitalCase($data) ) {
            $caseable = $this->getHospitalAccidentService()->create(
                $this->getHospitalAccidentData($data)
            );
        } else {
            Log::error('Caseable type should hospital or doctor', [$data]);
            throw new InconsistentDataException('Accidents caseable type is not correct');
        }

        return $caseable;
    }

    /**
     * @param int $id
     * @param array $data
     * @return Model
     * @throws InconsistentDataException
     */
    private function updateCaseable(int $id, array $data): Model
    {
        if ( $this->isDoctorCase($data) ) {
            $caseable = $this->updateTypedCaseable(
                $this->getDoctorAccidentService(),
                $id,
                $this->getDoctorAccidentData($data)
            );
        } elseif ( $this->isHospitalCase($data) ) {
            $caseable = $this->updateTypedCaseable(
                $this->getHospitalAccidentService(),
                $id,
                $this->getHospitalAccidentData($data)
            );
        } else {
            throw new InconsistentDataException('Accidents caseable type is not correct');
        }

        if (!$caseable) {
            throw new InconsistentDataException('Accidents caseable is not found');
        }

        return $caseable;
    }

    /**
     * @param AbstractModelService $service
     * @param int $id
     * @param array $caseableData
     * @return Model|null
     * @throws InconsistentDataException
     */
    private function updateTypedCaseable(AbstractModelService $service, int $id, array $caseableData): ?Model
    {
        if (count ($caseableData)) {
            if (
                array_key_exists(AbstractModelService::FIELD_ID, $caseableData)
                && $caseableData[AbstractModelService::FIELD_ID] === $id
            ) {
                return $service->findAndUpdate(
                    [AbstractModelService::FIELD_ID],
                    $caseableData
                );
            }
            throw new InconsistentDataException('Incorrect input parameters for the caseable. ID not found or not expected.');
        }

        return $service->first([AbstractModelService::FIELD_ID => $id]);
    }

    /**
     * @param array $data
     * @param AccidentAbstract $caseable
     * @param AccidentAbstract|null $previousCaseable
     */
    private function emitCaseableEvent(
        array $data,
        AccidentAbstract $caseable,
        AccidentAbstract $previousCaseable = null
    ): void
    {
        if ($this->isDoctorCase($data)) {
            event(new DoctorAccidentUpdatedEvent($caseable, $previousCaseable));
        } elseif ( $this->isHospitalCase($data) ) {
            event(new HospitalAccidentUpdatedEvent($caseable, $previousCaseable));
        }
    }

    /**
     * @param array $data
     * @return bool
     */
    private function isDoctorCase(array $data): bool
    {
        return $this->getCaseableType($data) === DoctorAccident::class;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function isHospitalCase(array $data): bool
    {
        return $this->getCaseableType($data) === HospitalAccident::class;
    }

    /**
     * @param Model $caseable
     * @param $data
     */
    private function updateCaseableMorphs(Model $caseable, $data): void
    {
        if ($this->isDoctorCase($data)) {

            $this->updateMorph($caseable, $data, self::PROPERTY_SERVICES);
            $this->updateMorphedServicesOrder($caseable, $data, self::PROPERTY_SERVICES, self::PROPERTY_SERVICEABLES);

            $this->updateMorph($caseable, $data, self::PROPERTY_SURVEYS);
            $this->updateMorphedSurveysOrder($caseable, $data, self::PROPERTY_SURVEYS, self::PROPERTY_SURVEABLES);

            $this->updateMorph($caseable, $data, self::PROPERTY_DIAGNOSTICS);
            $this->updateMorphedDiagnosticablesOrder($caseable, $data, self::PROPERTY_DIAGNOSTICS, self::PROPERTY_DIAGNOSTICABLES);
        }
    }

    /**
     * @param Model $caseable
     * @param array $data
     * @param string $key
     * @return void
     */
    private function updateMorph(Model $caseable, array $data, string $key): void
    {
        $caseable->$key()->detach();
        if (!empty($data[$key])) {
            $caseable->$key()->attach($data[$key]);
        }
    }

    private function updateMorphedServicesOrder(Model $caseable, array $data, string $key, string $table): void
    {
        if (!empty($data[$key])) {
            $order = 1;
            foreach ($data[$key] as $id) {
                $queryBuilder = DB::table($table);
                $queryBuilder->where([
                    'service_id' => $id,
                    'serviceable_type' => $caseable::class,
                    'serviceable_id' => $caseable->getAttribute(AbstractModelService::FIELD_ID),
                ]);
                $queryBuilder->update(['sort' => $order++]);
            }
        }
    }

    private function updateMorphedSurveysOrder(Model $caseable, array $data, string $key, string $table): void
    {
        if (!empty($data[$key])) {
            $order = 1;
            foreach ($data[$key] as $id) {
                $queryBuilder = DB::table($table);
                $queryBuilder->where([
                    'survey_id' => $id,
                    'surveable_type' => $caseable::class,
                    'surveable_id' => $caseable->getAttribute(AbstractModelService::FIELD_ID),
                ]);
                $queryBuilder->update(['sort' => $order++]);
            }
        }
    }

    private function updateMorphedDiagnosticablesOrder(Model $caseable, array $data, string $key, string $table): void
    {
        if (!empty($data[$key])) {
            $order = 1;
            foreach ($data[$key] as $id) {
                $queryBuilder = DB::table($table);
                $queryBuilder->where([
                    'diagnostic_id' => $id,
                    'diagnosticable_type' => $caseable::class,
                    'diagnosticable_id' => $caseable->getAttribute(AbstractModelService::FIELD_ID),
                ]);
                $queryBuilder->update(['sort' => $order++]);
            }
        }
    }

    /**
     * @return PatientService
     */
    private function getPatientService(): PatientService
    {
        return $this->getServiceLocator()->get(PatientService::class);
    }

    /**
     * @param array $data
     * @return Model|Patient|null
     */
    private function getPatient(array $data): Model|Patient|null
    {
        $patient = null;
        if (
            array_key_exists(self::PROPERTY_PATIENT, $data)
            && array_key_exists(PatientService::FIELD_ID, $data[self::PROPERTY_PATIENT])
            && $data[self::PROPERTY_PATIENT][PatientService::FIELD_ID]
        ) {
            $patient = $this->getPatientService()->first([PatientService::FIELD_ID => $data[self::PROPERTY_PATIENT][PatientService::FIELD_ID]]);
        }

        if (!$patient && array_key_exists(self::PROPERTY_PATIENT, $data)) {
            $patient = $this->getPatientService()->firstOrCreate($data[self::PROPERTY_PATIENT]);
        }

        return $patient;
    }

    /**
     * Update all the data with the request
     * @param array $filterByFields
     * @param array $data
     * @return Model|Accident
     * @throws InconsistentDataException
     */
    public function findAndUpdate(array $filterByFields, array $data): Model|Accident
    {
        $filter = [];
        foreach ($filterByFields as $field) {
            if (isset($data[$field])) {
                $filter[$field] = $data[$field];
            }
        }

        // requested by id
        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first($filter);

        if (!$accident) {
            throw new InconsistentDataException('Accident not found');
        }

        return $this->flushAccidentData($data, $accident);
    }

    /**
     * @param array $data
     * @return Model|Accident
     */
    public function firstOrCreate(array $data = []): Model|Accident
    {
        return $this->getAccidentService()->firstOrCreate($data);
    }

    /**
     * @param array $filters
     * @return Model|null|Accident
     */
    public function first(array $filters = []): Model|Accident|null
    {
        return $this->getAccidentService()->first($filters);
    }

    /**
     * @inheritDoc
     */
    public function search(array $filters = []): Collection
    {
        return new Collection(); // doesn't search
    }

    /**
     * @inheritDoc
     */
    public function count(array $filters = []): int
    {
        return 0; // doesn't count
    }

    /**
     * @param $id
     * @return bool
     * @throws InconsistentDataException
     */
    public function delete($id): bool
    {
        return $this->getAccidentService()->delete($id);
    }
}
