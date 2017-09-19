<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\AccidentType;
use App\Services\AccidentTypeService;
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
        if (env('APP_ENV') == 'production' && AccidentType::all()->count()) {
            return;
        }
        if (env('APP_ENV') == 'production') {
            AccidentType::truncate();
        }
        foreach (AccidentTypeService::ALLOWED_TYPES as $allowedType) {
            AccidentType::firstOrCreate(['title' => $allowedType]);
        }
    }
}
