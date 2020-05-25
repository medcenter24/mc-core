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
use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Entity\Diagnostic;
use medcenter24\mcCore\App\Entity\Disease;
use medcenter24\mcCore\App\Services\Entity\DiagnosticService;
use medcenter24\mcCore\App\Services\Entity\DiseaseService;
use medcenter24\mcCore\App\Services\Entity\DoctorService;

class DiagnosticTransformer extends AbstractTransformer
{

    /**
     * @inheritDoc
     */
    protected function getMap(): array
    {
        return [
            DiagnosticService::FIELD_ID,
            DiagnosticService::FIELD_TITLE,
            DiagnosticService::FIELD_DESCRIPTION,
            'diagnosticCategoryId' => DiagnosticService::FIELD_DIAGNOSTIC_CATEGORY_ID,
            DiagnosticService::FIELD_STATUS,
        ];
    }

    protected function getMappedTypes(): array
    {
        return [
            DiagnosticService::FIELD_ID => AbstractTransformer::VAR_INT,
            DiagnosticService::FIELD_DIAGNOSTIC_CATEGORY_ID => AccidentTransformer::VAR_INT,
        ];
    }

    /**
     * @param Model|Diagnostic $model
     * @return array
     */
    public function transform(Model $model): array {
        $fields = parent::transform($model);

        $createdBy = (int) $model->getAttribute(DiagnosticService::FIELD_CREATED_BY);
        $type = $createdBy ? 'director' : 'system';
        $fields['type'] = $this->getDoctorService()->isDoctor($createdBy) ? 'doctor' : $type;
        $fields[DiagnosticService::PROP_DISEASES] = $this->getTransformedDiseases($model->diseases);
        return $fields;
    }

    private function getTransformedDiseases(Collection $diseases): array
    {
        $diseaseTransformer = new DiseaseTransformer();
        $res = [];
        $diseases->each(static function (Disease $disease) use ($diseaseTransformer, &$res) {
            $res[] = $diseaseTransformer->transform($disease);
        });
        return $res;
    }

    /**
     * @return DoctorService
     */
    private function getDoctorService(): DoctorService {
        return $this->getServiceLocator()->get(DoctorService::class);
    }

    public function inverseTransform(array $data): array
    {
        $transformed = parent::inverseTransform($data);
        if (isset($data['diseases']) && is_array($data['diseases'])) {
            $transformed['diseases'] = [];
            foreach ($data['diseases'] as $disease) {
                if (isset($disease['id'])) {
                    $transformed['diseases'][] = $this->getDiseaseService()->first([DiseaseService::FIELD_ID => $disease['id']]);
                }
            }
        }
        return $transformed;
    }

    private function getDiseaseService(): DiseaseService
    {
        return $this->getServiceLocator()->get(DiseaseService::class);
    }
}
