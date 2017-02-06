<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\Role;
use App\User;
use Illuminate\Database\Seeder;

class UsersRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_user')->delete();

        $loginRole = Role::where('title', Role::ROLE_LOGIN)->first()->id;

        $admin = User::where('email', 'admin@mail.com')->first();
        $doctor = User::where('email', 'doctor@mail.com')->first();
        $director = User::where('email', 'director@mail.com')->first();

        $admin->roles()->attach([$loginRole, Role::where('title', Role::ROLE_ADMIN)->first()->id]);
        $doctor->roles()->attach([$loginRole, Role::where('title', Role::ROLE_DOCTOR)->first()->id]);
        $director->roles()->attach([$loginRole, Role::where('title', Role::ROLE_DIRECTOR)->first()->id]);
    }
}
