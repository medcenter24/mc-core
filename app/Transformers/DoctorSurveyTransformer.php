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


use medcenter24\mcCore\App\DoctorSurvey;
use medcenter24\mcCore\App\Services\DoctorServiceService;

class DoctorSurveyTransformer extends AbstractTransformer
{
    public function transform(DoctorSurvey $doctorSurvey): array
    {
        $createdBy = $doctorSurvey->getAttribute('created_by');
        $type = $createdBy ? 'director' : 'system';
        return [
            'id' => $doctorSurvey->getAttribute('id'),
            'title' => $doctorSurvey->getAttribute('title'),
            'description' => $doctorSurvey->getAttribute('description'),
            'type' => $this->getDoctorService()->isDoctor($createdBy) ? 'doctor' : $type,
            'status' => $doctorSurvey->getAttribute('status'),
            'diseaseCode' => $doctorSurvey->getAttribute('disease_code'),
        ];
    }

    private function getDoctorService(): DoctorServiceService {
        return $this->getServiceLocator()->get(DoctorServiceService::class);
    }
}
