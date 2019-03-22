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
use App\Role;
use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('production') && \App\User::all()->count()) {
            return;
        } elseif (!App::environment('production')) {
            User::truncate();
            // DB::table('role_user')->delete();
            // $loginRoleId = Role::firstOrCreate(['title' => Role::ROLE_LOGIN])->id;
            // $adminRoleId = Role::firstOrCreate(['title' => Role::ROLE_ADMIN])->id;
            // $directorRoleId = Role::firstOrCreate(['title' => Role::ROLE_DIRECTOR])->id;
            // factory(User::class, 10)->create();

            $director = factory(User::class)->create([
                'email' => 'director@mail.com',
                'name' => 'Samantha',
            ]);

            $director->roles()->attach([$loginRoleId, $directorRoleId]);

            $admin = User::firstOrCreate([
                'email' => 'zagovorichev@gmail.com',
                'name' => 'Alexander Zagovorichev',
                'password' => bcrypt('secret')
            ]);

            $admin->roles()->attach([$loginRoleId, $adminRoleId]);
        }
    }
}
