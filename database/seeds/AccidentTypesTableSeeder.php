<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use Illuminate\Database\Seeder;

class AccidentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\AccidentType::truncate();
        factory(\App\AccidentType::class)->create(['title' => 'Insurance']);
        factory(\App\AccidentType::class)->create(['title' => 'Not insurance']);
    }
}
