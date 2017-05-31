<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

/** @var \Dingo\Api\Routing\Router $api */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function($api) {
    $api->post('authenticate', '\App\Http\Controllers\Api\V1\AuthenticateController@authenticate');
});

$api->version('v1', ['middleware' => ['api.auth']], function ($api) {

    $api->post('logout', '\App\Http\Controllers\Api\V1\AuthenticateController@logout');
    $api->get('token', '\App\Http\Controllers\Api\V1\AuthenticateController@getToken');

    $api->group(['prefix' => 'director', 'middleware' => ['cors']], function ($api) {
        $api->post('cases/importer', '\App\Http\Controllers\Api\V1\Director\CasesImporterController@upload');
        $api->put('cases/importer', '\App\Http\Controllers\Api\V1\Director\CasesImporterController@import');
        $api->resource('cases', \App\Http\Controllers\Api\V1\Director\CasesController::class);
    });
});
