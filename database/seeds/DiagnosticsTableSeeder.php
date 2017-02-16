<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\Diagnostic;
use Illuminate\Database\Seeder;

class DiagnosticsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Diagnostic::truncate();
        factory(Diagnostic::class, 3)->create();
    }
}
