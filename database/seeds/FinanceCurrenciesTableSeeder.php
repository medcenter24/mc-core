<?php

use App\FinanceCurrency;
use Illuminate\Database\Seeder;

class FinanceCurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FinanceCurrency::truncate();
        factory(FinanceCurrency::class, 3)->create();
    }
}
