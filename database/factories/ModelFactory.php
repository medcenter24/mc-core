<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

if (!function_exists('getRandomAccidentStatus')) {
    /**
     * @param \Faker\Generator $faker
     * @return \App\AccidentStatus
     */
    function getRandomAccidentStatus(\Faker\Generator $faker)
    {
        $status = $faker->randomElement(AccidentStatusesTableSeeder::ACCIDENT_STATUSES);

        $_status = \App\AccidentStatus::where('title', $status['title'])
            ->where('type', $status['type'])
            ->first();

        return $_status && $_status->id ? $_status : factory(\App\AccidentStatus::class)->create($status);
    }
}
