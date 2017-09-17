<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\Assistant;
use Illuminate\Database\Seeder;

class AssistantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Assistant::truncate();
        factory(Assistant::class, 10)->create();
    }
}
