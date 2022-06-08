<?php
/*
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
 * Copyright (c) 2022 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace medcenter24\mcCore\App\Services\Accident\Diagnostic;

use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\User;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\DoctorAccidentService;
use medcenter24\mcCore\App\Services\Entity\RoleService;
use medcenter24\mcCore\App\Services\Entity\UserService;

class AccidentDiagnosticService
{
    public function __construct(
        protected RoleService $roleService,
        protected AccidentService $accidentServiceService,
        protected DoctorAccidentService $doctorAccidentService,
        protected UserService $userService,
    ) { }

    public function getAccidentDiagnostics(Accident $accident): Collection
    {
        if ($this->accidentServiceService->isDoctorAccident($accident)) {
            $services = $this->getDoctorAccidentDiagnostics($accident);
        }
        return $services ?? collect();
    }

    private function getDoctorAccidentDiagnostics(Accident $accident): Collection
    {
        $diagnostics = $this->doctorAccidentService
            ->getSortedDiagnostics($accident->getAttribute(AccidentService::FIELD_ID));

        $roleService = $this->roleService;
        $diagnostics->each(function ($diagnostic) use ($roleService) {
            if ($diagnostic->created_by) {
                /** @var User $user */
                $user = $this->userService->first([UserService::FIELD_ID => $diagnostic->created_by]);
                $diagnostic->isDoctor = empty($user) || $roleService->hasRole($user, 'doctor');
            }
        });
        return $diagnostics;
    }
}
