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

namespace medcenter24\mcCore\App\Transformers;

use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\SurveysController;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\CaseAccidentService;

/**
 * Used for the output into the data table
 * Class CasesTransformer
 * @package medcenter24\mcCore\App\Transformers
 */
class CaseAccidentTransformer extends AbstractTransformer
{
    public const CASE_TYPE_DOCTOR = 'doctor';
    public const CASE_TYPE_HOSPITAL = 'hospital';
    private const CASE_TYPE_UNDEFINED = 'undefined';

    public const CASE_TYPE_MAP = [
        DoctorAccident::class => self::CASE_TYPE_DOCTOR,
        HospitalAccident::class => self::CASE_TYPE_HOSPITAL
    ];

    /**
     * @inheritDoc
     */
    public function transform (Model $model): array
    {
        $fields = parent::transform($model);
        $fields['patientName'] = $model->getAttribute('patient')
            ? $model->getAttribute('patient')->getAttribute('name')
            : '';
        $fields['checkpoints'] = $model->getAttribute('checkpoints')
            ? $model->getAttribute('checkpoints')->implode('title', ', ')
            : '';
        $fields['statusTitle'] = $model->getAttribute('accidentStatus')
            ? $model->getAttribute('accidentStatus')->getAttribute('title')
            : '';
        $fields['cityTitle'] = $model->getAttribute('city')
            ? $model->getAttribute('city')->getAttribute('title')
            : '';
        $fields['price'] = (float) ($model->getAttribute('incomePayment')
            ? $model->getAttribute('incomePayment')->getAttribute('value')
            : 0);
        $fields['doctorsFee'] = (float) ($model->getAttribute('paymentToCaseable')
            ? $model->getAttribute('paymentToCaseable')->getAttribute('value')
            : 0);
        $fields['caseType'] = $this->getTransformedCaseType($model);

        return $fields;
    }

    /**
     * @inheritDoc
     */
    protected function getMap(): array
    {
        return [
            AccidentService::FIELD_ID,
            'assistantId' => AccidentService::FIELD_ASSISTANT_ID,
            'repeated' => AccidentService::FIELD_PARENT_ID,
            'refNum' => AccidentService::FIELD_REF_NUM,
            'assistantRefNum' => AccidentService::FIELD_ASSISTANT_REF_NUM,
            'symptoms' => AccidentService::FIELD_SYMPTOMS,
            'createdAt' => AccidentService::FIELD_CREATED_AT,
            'handlingTime' => AccidentService::FIELD_HANDLING_TIME,
        ];
    }

    /**
     * This is not a model but a collection of the models
     * so input parameters have differences from the output
     * @param array $data
     * @return array
     * @throws InconsistentDataException
     */
    public function inverseTransform(array $data): array
    {
        // only expected parameters
        foreach ($data as $key => $datum) {
            switch ($key) {
                case CaseAccidentService::PROPERTY_ACCIDENT:
                    $transformer = new AccidentTransformer();
                    break;
                case CaseAccidentService::PROPERTY_DOCTOR_ACCIDENT:
                    $transformer = new DoctorAccidentTransformer();
                    break;
                case CaseAccidentService::PROPERTY_HOSPITAL_ACCIDENT:
                    $transformer = new HospitalAccidentTransformer();
                    break;
                case CaseAccidentService::PROPERTY_SERVICES:
                    $transformer = new ServiceTransformer();
                    break;
                case CaseAccidentService::PROPERTY_DIAGNOSTICS:
                    $transformer = new DiagnosticTransformer();
                    break;
                case CaseAccidentService::PROPERTY_SURVEYS:
                    $transformer = new SurveyTransformer();
                    break;
                case CaseAccidentService::PROPERTY_DOCUMENTS:
                    $transformer = new DocumentTransformer();
                    break;
                case CaseAccidentService::PROPERTY_CHECKPOINTS:
                    $transformer = new AccidentCheckpointTransformer();
                    break;
                case CaseAccidentService::PROPERTY_PATIENT:
                    $transformer = new PatientTransformer();
                    break;
                default: unset($data[$key]);
            }

            if (isset($transformer)) {
                $data[$key] = $transformer->inverseTransform($data[$key]);
            }
        }
        
        // transform case type if provided
        $data = $this->inverseTransformCaseType($data);
        return $data;
    }

    /**
     * @param array $data
     * @return array
     * @throws InconsistentDataException
     */
    private function inverseTransformCaseType(array $data): array
    {
        if (
            array_key_exists(CaseAccidentService::PROPERTY_ACCIDENT, $data)
            && array_key_exists(AccidentService::FIELD_CASEABLE_TYPE, $data[CaseAccidentService::PROPERTY_ACCIDENT])
        ) {
            $incType = $data[CaseAccidentService::PROPERTY_ACCIDENT][AccidentService::FIELD_CASEABLE_TYPE] ?: self::CASE_TYPE_DOCTOR;
            $invMap = array_flip(self::CASE_TYPE_MAP);
            if (!array_key_exists($incType, $invMap)) {
                throw new InconsistentDataException('Case type is not correct');
            }
            $data[CaseAccidentService::PROPERTY_ACCIDENT][AccidentService::FIELD_CASEABLE_TYPE] = $invMap[$incType];
        }
        return $data;
    }
    
    /**
     * @inheritDoc
     */
    protected function getMappedTypes(): array
    {
        return [
            AccidentService::FIELD_ASSISTANT_ID => self::VAR_INT,
            AccidentService::FIELD_ID => self::VAR_INT,
            AccidentService::FIELD_PARENT_ID => self::VAR_INT,
            AccidentService::FIELD_CREATED_AT => self::VAR_DATETIME,
            AccidentService::FIELD_HANDLING_TIME => self::VAR_DATETIME,
        ];
    }

    private function getTransformedCaseType(Model $model): string
    {
        $caseType = $model->getAttribute(AccidentService::FIELD_CASEABLE_TYPE);
        return array_key_exists($caseType, self::CASE_TYPE_MAP) ? self::CASE_TYPE_MAP[$caseType] : self::CASE_TYPE_UNDEFINED;
    }
}
