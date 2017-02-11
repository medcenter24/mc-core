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
        $docs = factory(AccidentDoctor::class, 10)->make();
        foreach($docs as $doc) {
            $doc->save();
        }
    }
}
