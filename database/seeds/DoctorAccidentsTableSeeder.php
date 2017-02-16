<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\DoctorAccident;
use Illuminate\Database\Seeder;

class DoctorAccidentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DoctorAccident::truncate();

        // 10 doc accidents without docs
        factory(DoctorAccident::class, 10)->create();
        // 10 doc accidents with docs
        factory(DoctorAccident::class, 10)
            ->create()
            ->each(function ($doctorAccident) {
                $doctorAccident->documents()->save(factory(\App\Document::class)->make());
            });
    }
}
