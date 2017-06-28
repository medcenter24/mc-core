<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\AccidentStatus;
use Illuminate\Database\Seeder;

class AccidentStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AccidentStatus::truncate();
        foreach (\App\Services\AccidentStatusesService::ACCIDENT_STATUSES as $accidentStatus) {
            AccidentStatus::firstOrCreate($accidentStatus);
        }
    }
}
