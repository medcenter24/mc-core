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

namespace medcenter24\mcCore\App\Transformers;


use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\HospitalAccidentService;

class HospitalCaseTransformer extends AbstractTransformer
{
    public function transform(Model $model): array
    {
        $fields = parent::transform($model);
        $accident = $this->getAccident($model);
        $fields['accidentId'] = (int) $accident->getAttribute(AccidentService::FIELD_ID);
        $fields['assistantInvoiceId'] = (int) $accident->getAttribute(AccidentService::FIELD_ASSISTANT_INVOICE_ID);
        $fields['assistantGuaranteeId'] = (int) $accident->getAttribute(AccidentService::FIELD_ASSISTANT_GUARANTEE_ID);
        return $fields;
    }

    private function getAccident(Model $model): Accident
    {
        return $model->getAttribute('accident');
    }

    protected function getMap(): array
    {
        return [
            HospitalAccidentService::FIELD_ID,
            'hospitalId' => HospitalAccidentService::FIELD_HOSPITAL_ID,
            'hospitalGuaranteeId' => HospitalAccidentService::FIELD_HOSPITAL_GUARANTEE_ID,
            'hospitalInvoiceId' => HospitalAccidentService::FIELD_HOSPITAL_INVOICE_ID,
        ];
    }

    protected function getMappedTypes(): array
    {
        return [
            HospitalAccidentService::FIELD_ID => self::VAR_INT,
            HospitalAccidentService::FIELD_HOSPITAL_ID => self::VAR_INT,
            HospitalAccidentService::FIELD_HOSPITAL_GUARANTEE_ID => self::VAR_INT,
            HospitalAccidentService::FIELD_HOSPITAL_INVOICE_ID => self::VAR_INT,
        ];
    }
}
