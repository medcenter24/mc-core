<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\AccidentCheckpoint;
use Illuminate\Database\Seeder;

class AccidentCheckpointsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('accident_accident_checkpoint')->delete();
        AccidentCheckpoint::truncate();
        factory(AccidentCheckpoint::class, 10)->create();
    }
}
