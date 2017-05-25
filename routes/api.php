<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

/** @var \Dingo\Api\Routing\Router $api */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['prefix' => 'director', 'middleware' => ['cors']], function ($api) {
        $api->resource('cases', \App\Http\Controllers\Api\V1\Director\CasesController::class);
    });
});
