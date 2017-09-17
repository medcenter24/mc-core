<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\FormReport;
use Illuminate\Database\Seeder;

class FormReportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FormReport::truncate();
        factory(FormReport::class, 10)->create();
    }
}
