<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\Discount;
use Illuminate\Database\Seeder;

class DiscountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Discount::truncate();
        foreach (\App\Services\DiscountService::ALLOWED_OPERATIONS as $allowed) {
            factory(Discount::class)->create([
                'title' => $allowed,
                'operation' => $allowed
            ]);
        }
    }
}
