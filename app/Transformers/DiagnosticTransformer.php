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

use medcenter24\mcCore\App\Diagnostic;
use medcenter24\mcCore\App\Services\DoctorServiceService;

class DiagnosticTransformer extends AbstractTransformer
{
    /**
     * @param Diagnostic $diagnostic
     * @return array
     */
    public function transform(Diagnostic $diagnostic): array
    {
        $createdBy = $diagnostic->getAttribute('created_by');
        $type = $createdBy ? 'director' : 'system';
        return [
            'id' => $diagnostic->id,
            'title' => $diagnostic->title,
            'description' => $diagnostic->description,
            'diagnosticCategoryId' => $diagnostic->diagnostic_category_id,
            'diseaseCode' => $diagnostic->disease_code,
            'type' => $this->getDoctorService() ? 'doctor' : $type,
            'status' => $diagnostic->getAttribute('status'),
        ];
    }

    private function getDoctorService(): DoctorServiceService {
        return $this->getServiceLocator()->get(DoctorServiceService::class);
    }
}
