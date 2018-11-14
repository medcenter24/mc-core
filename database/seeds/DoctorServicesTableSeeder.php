<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\DoctorService;
use Illuminate\Database\Seeder;

class DoctorServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('production') && DoctorService::all()->count()) {
            return;
        } elseif (!App::environment('production')) {
            DoctorService::truncate();
            factory(DoctorService::class, 10)->create();
        }
    }
}
