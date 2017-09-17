<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\DiagnosticCategory;
use Illuminate\Database\Seeder;

class DiagnosticCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DiagnosticCategory::truncate();
        factory(DiagnosticCategory::class, 3)->create();
    }
}
