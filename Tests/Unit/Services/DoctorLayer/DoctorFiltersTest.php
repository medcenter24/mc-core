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

namespace medcenter24\mcCore\Tests\Unit\Services\DoctorLayer;


use Illuminate\Foundation\Testing\DatabaseMigrations;
use medcenter24\mcCore\App\Entity\Diagnostic;
use medcenter24\mcCore\App\Services\Entity\DiagnosticService;
use medcenter24\mcCore\App\Services\Entity\RoleService;
use medcenter24\mcCore\App\Services\Entity\UserService;
use medcenter24\mcCore\Tests\TestCase;

class DoctorFiltersTest extends TestCase
{
    use DatabaseMigrations;
    
    public function testGetActiveByDoctor(): void
    {
        $service = new DiagnosticService();
        // found by created
        $service->create([
            'created_by' => 0,
            'title' => 'diagnostic 1',
        ]);
        $userService = new UserService();
        $director = $userService->create([
            'name' => 'director',
        ]);
        $roleService = new RoleService();
        $directorRole = $roleService->create([
            'title' => RoleService::DIRECTOR_ROLE,
        ]);
        $director->roles()->attach([$directorRole->id]);

        $service->create([
            'created_by' => $director->id,
            'title' => 'diagnostic 2',
        ]);
        $result = $service->getActiveByDoctor(0);
        $this->assertCount(2, Diagnostic::all());
        $this->assertCount(2, $result);
    }
}
