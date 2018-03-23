<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
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
        if (env('APP_ENV') == 'production' && Doctor::all()->count()) {
            return;
        } elseif (env('APP_ENV') != 'production') {
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
