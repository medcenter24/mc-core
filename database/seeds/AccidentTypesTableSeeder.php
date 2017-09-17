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
        AccidentType::truncate();
        foreach (AccidentTypeService::ALLOWED_TYPES as $allowedType) {
            $attr = ['title' => $allowedType];
            if (!AccidentType::find($attr)->count()) {
                factory(AccidentType::class)->create($attr);
            }
        }
    }
}
