<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

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
        User::truncate();
        DB::table('role_user')->delete();

        factory(User::class, 10)->create();

        $loginRoleId = Role::where('title', Role::ROLE_LOGIN)->first()->id;
        $adminRoleId = Role::where('title', Role::ROLE_ADMIN)->first()->id;
        $doctorRoleId = Role::where('title', Role::ROLE_DOCTOR)->first()->id;
        $directorRoleId = Role::where('title', Role::ROLE_DIRECTOR)->first()->id;

        $admin = factory(User::class)->create([
            'email' => 'admin@mail.com',
            'name' => 'Abigail',
        ]);

        $admin->roles()->attach([$loginRoleId, $adminRoleId]);

        $doctor = factory(\App\Doctor::class)->create([
            'name' => 'Doctor Aibolit',
            'user_id' => function() {
                return factory(User::class)->create([
                    'email' => 'doctor@mail.com',
                    'name' => 'Peter',
                ]);
            }
        ]);

        $doctor->user->roles()->attach([$loginRoleId, $doctorRoleId]);

        $director = factory(User::class)->create([
            'email' => 'director@mail.com',
            'name' => 'Samantha',
        ]);

        $director->roles()->attach([$loginRoleId, $directorRoleId]);
    }
}
