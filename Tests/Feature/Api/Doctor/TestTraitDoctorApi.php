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

namespace medcenter24\mcCore\Tests\Feature\Api\Doctor;

use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\User;
use medcenter24\mcCore\App\Services\Entity\DoctorService;
use medcenter24\mcCore\App\Services\Entity\RoleService;
use medcenter24\mcCore\Tests\Feature\Api\TestTraitApi;

trait TestTraitDoctorApi
{
    use TestTraitApi;

    /**
     * @var Doctor
     */
    private $doctor;

    protected function getLoggedUser(): User
    {
        if (!$this->user) {
            $this->user = $this->getUser([RoleService::DOCTOR_ROLE]);
        }
        return $this->user;
    }

    protected function getCurrentDoctor(): Doctor
    {
        if (!$this->doctor) {
            $this->doctor = factory(Doctor::class)->create([
                DoctorService::FIELD_USER_ID => $this->getLoggedUser()->getKey(),
            ]);
        }
        return $this->doctor;
    }

    protected function getHeaders(): array
    {
        // create Doctor instance for the User (otherwise it won't work)
        $this->getCurrentDoctor();
        return $this->headers($this->getLoggedUser());
    }
}
