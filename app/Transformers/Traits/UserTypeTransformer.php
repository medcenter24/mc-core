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
use medcenter24\mcCore\App\Services\Entity\DoctorService;

trait UserTypeTransformer
{
    protected function getCreatorUserType(Model $model): string
    {
        $createdBy = (int) $model->getAttribute('created_by');
        $type = $createdBy ? 'director' : 'system';
        return $this->getDoctorService()->isDoctor($createdBy) ? 'doctor' : $type;
    }

    /**
     * @return DoctorService
     */
    private function getDoctorService(): DoctorService {
        return $this->getServiceLocator()->get(DoctorService::class);
    }
}
