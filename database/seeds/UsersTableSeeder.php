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
     * @throws ErrorException
     */
    public function run()
    {
        $loginRoleId = Role::firstOrCreate(['title' => Role::ROLE_LOGIN])->id;
        $adminRoleId = Role::firstOrCreate(['title' => Role::ROLE_ADMIN])->id;
        $doctorRoleId = Role::firstOrCreate(['title' => Role::ROLE_DOCTOR])->id;
        $directorRoleId = Role::firstOrCreate(['title' => Role::ROLE_DIRECTOR])->id;

        if (env('APP_ENV') == 'production' && \App\User::all()->count()) {
            return;
        } elseif (env('APP_ENV') != 'production') {
            User::truncate();
            DB::table('role_user')->delete();
            factory(User::class, 10)->create();

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

        $admin = factory(User::class)->create([
            'email' => 'zagovorichev@gmail.com',
            'name' => 'Alexander Zagovorichev',
        ]);

        $admin->roles()->attach([$loginRoleId, $adminRoleId]);
    }
}
