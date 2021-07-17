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

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Services\Entity\RoleService;
use medcenter24\mcCore\Tests\Feature\Api\TestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class NotAssignedDoctorTest extends TestCase
{

    use TestTraitApi;

    protected function getHeaders(): array
    {
        return $this->headers($this->getUser([RoleService::DOCTOR_ROLE, RoleService::LOGIN_ROLE]));
    }

    public function testAccess(): void
    {
        $accident = Accident::factory()->create();
        $response = $this->sendGet('/api/doctor/accidents/'.$accident->id.'/diagnostics');
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Current user should be assigned to a doctor',
            'status_code' => 403,
        ]);
    }
}