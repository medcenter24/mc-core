<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

use App\FinanceCondition;
use Illuminate\Database\Seeder;

class FinanceConditionTableSeeder extends Seeder
{
    public function run()
    {
        FinanceCondition::truncate();
        factory(FinanceCondition::class, 3)->create();
    }
}
