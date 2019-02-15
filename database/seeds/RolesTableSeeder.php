<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('production') && \App\Role::all()->count()) {
            return;
        }
        if (!App::environment('production')) {
            Role::truncate();
        }

        foreach ([Role::ROLE_LOGIN, Role::ROLE_ADMIN, Role::ROLE_DOCTOR, Role::ROLE_DIRECTOR] as $roleName) {
            Role::firstOrCreate(['title' => $roleName]);
        }
    }
}
