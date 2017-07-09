<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\AccidentCheckpoint;
use Illuminate\Database\Seeder;

class AccidentCheckpointsTableProdSeeder extends Seeder
{
    private $data = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('accident_accident_checkpoint')->delete();
        AccidentCheckpoint::truncate();
        factory(AccidentCheckpoint::class)->create();
    }
}
