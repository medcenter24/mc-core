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
use medcenter24\mcCore\App\Entity\Role;
use medcenter24\mcCore\App\Entity\User;
use Illuminate\Database\Seeder;
use medcenter24\mcCore\App\Services\Entity\RoleService;

class UsersTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('production') && User::all()->count()) {
            return;
        } elseif (!App::environment('production')) {
            User::truncate();
            $loginRoleId = Role::firstOrCreate(['title' => RoleService::LOGIN_ROLE])->id;
            $adminRoleId = Role::firstOrCreate(['title' => RoleService::ADMIN_ROLE])->id;
            $directorRoleId = Role::firstOrCreate(['title' => RoleService::DIRECTOR_ROLE])->id;

            $director = User::factory()->create([
                'email' => 'director@mail.com',
                'name' => 'Samantha',
            ]);

            $director->roles()->attach([$loginRoleId, $directorRoleId]);

            $admin = User::firstOrCreate([
                'email' => 'test@example.com',
                'name' => 'User Name',
                'password' => bcrypt('secret')
            ]);

            $admin->roles()->attach([$loginRoleId, $adminRoleId]);
        }
    }
}
