<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */


$factory->define(\App\FinanceCondition::class, function () {
    return [
        'created_by' => 0, // default for the system
        'title' => 'Finance condition',
        'price' => mt_rand(1, 999),
    ];
});
