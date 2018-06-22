<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

use App\FinanceStorage;
use Illuminate\Database\Seeder;


class FinanceStorageTableSeeder extends Seeder
{
    public function run()
    {
        FinanceStorage::truncate();
        factory(FinanceStorage::class, 3)->create();
    }
}
