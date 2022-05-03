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

declare(strict_types=1);

namespace medcenter24\mcCore\App\Transformers\Traits;

use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\CaseAccidentService;

/**
 * Doctors case or Hospital case
 * Trait CaseTypeTransformer
 * @package medcenter24\mcCore\App\Transformers\Traits
 */
trait CaseTypeTransformer
{
    private function getTransformedDoctorCase(): string
    {
        return 'doctor';
    }

    private function getTransformedHospitalCase(): string
    {
        return 'hospital';
    }

    #[ArrayShape([DoctorAccident::class => "string", HospitalAccident::class => "string"])]
    public function getCaseTypeMap(): array
    {
        return [
            DoctorAccident::class => $this->getTransformedDoctorCase(),
            HospitalAccident::class => $this->getTransformedHospitalCase(),
        ];
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
            && array_key_exists(
                AccidentService::FIELD_CASEABLE_TYPE,
                $data[CaseAccidentService::PROPERTY_ACCIDENT]
            )
        ) {
            $incType = $data[CaseAccidentService::PROPERTY_ACCIDENT][AccidentService::FIELD_CASEABLE_TYPE]
                ?: $this->getTransformedDoctorCase();
            $data[CaseAccidentService::PROPERTY_ACCIDENT][AccidentService::FIELD_CASEABLE_TYPE] = $this->getInverseTransformedType($incType);
        }
        return $data;
    }

    private function getTransformedCaseType(Model $model): string
    {
        $caseType = $model->getAttribute(AccidentService::FIELD_CASEABLE_TYPE);
        return $this->getTransformedType($caseType);
    }

    /**
     * @param string $guiType
     * @return string
     * @throws InconsistentDataException
     */
    public function getInverseTransformedType(string $guiType): string
    {
        $invMap = array_flip($this->getCaseTypeMap());
        if (!array_key_exists($guiType, $invMap)) {
            throw new InconsistentDataException('Type "'.$guiType.'" is not expected');
        }
        return $invMap[$guiType];
    }

    public function getTransformedType(string $caseType): string
    {
        return array_key_exists($caseType, $this->getCaseTypeMap())
            ? $this->getCaseTypeMap()[$caseType]
            : 'undefined';
    }
}
