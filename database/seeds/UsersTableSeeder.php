<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
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
