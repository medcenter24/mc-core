<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\DatePeriod;
use Illuminate\Database\Seeder;

class DatePeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DatePeriod::truncate();
        factory(DatePeriod::class, 100)->create();
    }
}
