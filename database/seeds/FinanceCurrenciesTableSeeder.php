<?php
/**
 * Copyright (c) 2019.
 *
 * Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

use App\FinanceCurrency;
use Illuminate\Database\Seeder;

class FinanceCurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        FinanceCurrency::truncate();
        factory(FinanceCurrency::class)->create([
            'title' => 'Euro',
            'code' => 'eur',
            'ico' => 'fa fa-euro',
        ]);
    }
}
