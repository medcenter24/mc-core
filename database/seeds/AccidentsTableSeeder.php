<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\Accident;
use App\AccidentCheckpoint;
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
        factory(Accident::class, 100)->create()->each(function(Accident $accident) {
            $accident->checkpoints()->save(factory(AccidentCheckpoint::class)->create());
        });
    }
}
