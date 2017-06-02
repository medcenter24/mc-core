<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\Accident;
use App\AccidentCheckpoint;
use App\Diagnostic;
use App\DoctorService;
use Illuminate\Database\Seeder;

class AccidentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('accident_accident_checkpoint')->delete();
        Accident::truncate();
        factory(Accident::class, 5)->create()->each(function(Accident $accident) {
            $accident->checkpoints()->save(factory(AccidentCheckpoint::class)->create());
            $accident->services()->attach(factory(DoctorService::class)->create());
            $accident->services()->attach(factory(DoctorService::class)->create());
            $accident->caseable->services()->attach(factory(DoctorService::class)->create());

            $accident->diagnostics()->attach(factory(Diagnostic::class)->create());
            $accident->diagnostics()->attach(factory(Diagnostic::class)->create());
            $accident->caseable->diagnostics()->attach(factory(Diagnostic::class)->create());
        });
    }
}
