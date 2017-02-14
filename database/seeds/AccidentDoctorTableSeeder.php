<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\AccidentDoctor;
use Illuminate\Database\Seeder;

class AccidentDoctorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AccidentDoctor::truncate();
        DB::table('accident_doctor_document')->delete();

        // 10 doc accidents without docs
        factory(AccidentDoctor::class, 10)->create();
        // 10 doc accidents with docs
        factory(AccidentDoctor::class, 10)
            ->create()
            ->each(function ($d) {
                $d->documents()->save(factory(\App\Document::class)->make());
            });
    }
}
