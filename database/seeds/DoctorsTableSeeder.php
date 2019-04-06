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

use App\City;
use App\Doctor;
use App\Role;
use App\User;
use Illuminate\Database\Seeder;

class DoctorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('production') && Doctor::all()->count()) {
            return;
        } elseif (App::environment('production')) {
            Doctor::truncate();

            factory(Doctor::class, 10)->create([
                'city_id' => function() {
                    return factory(City::class)->create()->id;
                }
            ]);

            $loginRoleId = Role::firstOrCreate(['title' => Role::ROLE_LOGIN])->id;
            $doctorRoleId = Role::firstOrCreate(['title' => Role::ROLE_DOCTOR])->id;

            $doctor = factory(\App\Doctor::class)->create([
                'name' => 'Doctor Aibolit',
                'user_id' => function() {
                    return factory(User::class)->create([
                        'email' => 'doctor@mail.com',
                        'name' => 'Peter',
                    ]);
                },
                'city_id' => function() {
                    return factory(City::class)->create()->id;
                }
            ]);
            $doctor->user->roles()->attach([$loginRoleId, $doctorRoleId]);
        }
    }
}
