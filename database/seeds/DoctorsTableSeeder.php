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

namespace Database\Seeders;

use Illuminate\Support\Facades\App;
use medcenter24\mcCore\App\Entity\City;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\Role;
use medcenter24\mcCore\App\Services\Entity\RoleService;
use medcenter24\mcCore\App\Entity\User;
use Illuminate\Database\Seeder;

class DoctorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        if (App::environment('production') && Doctor::all()->count()) {
            return;
        } elseif (App::environment('production')) {
            Doctor::truncate();

            Doctor::factory()->count(10)->create([
                'city_id' => function() {
                    return City::factory()->create()->id;
                }
            ]);

            $loginRoleId = Role::firstOrCreate(['title' => RoleService::LOGIN_ROLE])->id;
            $doctorRoleId = Role::firstOrCreate(['title' => RoleService::DOCTOR_ROLE])->id;

            $doctor = Doctor::factory()->create([
                'name' => 'Doctor Aibolit',
                'user_id' => function() {
                    return User::factory()->create([
                        'email' => 'doctor@mail.com',
                        'name' => 'Peter',
                    ]);
                },
                'city_id' => function() {
                    return City::factory()->create()->id;
                }
            ]);
            $doctor->user->roles()->attach([$loginRoleId, $doctorRoleId]);
        }
    }
}
