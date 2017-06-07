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
    $api->get('user', '\App\Http\Controllers\Api\V1\AuthenticateController@authenticatedUser');

    $api->group(['prefix' => 'doctor', 'middleware' => ['cors', 'role:doctor']], function ($api) {
        $api->resource('accidents', \App\Http\Controllers\Api\V1\Doctor\AccidentsController::class);
        $api->get('me', '\App\Http\Controllers\Api\V1\AuthenticateController@authenticatedUser');
    });

    $api->group(['prefix' => 'director', 'middleware' => ['cors', 'role:director']], function ($api) {

        // Importer
        $api->post('cases/importer', '\App\Http\Controllers\Api\V1\Director\CasesImporterController@upload');
        $api->get('cases/importer', '\App\Http\Controllers\Api\V1\Director\CasesImporterController@uploads');
        $api->put('cases/importer/{id}', '\App\Http\Controllers\Api\V1\Director\CasesImporterController@import');
        $api->delete('cases/importer/{id}', '\App\Http\Controllers\Api\V1\Director\CasesImporterController@destroy');

        // Cases
        $api->get('cases/{id}/doctorcase', '\App\Http\Controllers\Api\V1\Director\CasesController@getDoctorCase');
        $api->get('cases/{id}/hospitalcase', '\App\Http\Controllers\Api\V1\Director\CasesController@getHospitalCase');
        $api->get('cases/{id}/diagnostics', '\App\Http\Controllers\Api\V1\Director\CasesController@getDiagnostics');
        $api->get('cases/{id}/services', '\App\Http\Controllers\Api\V1\Director\CasesController@getServices');

        $api->resource('cases', \App\Http\Controllers\Api\V1\Director\CasesController::class);

        $api->get('accidents/{id}', '\App\Http\Controllers\Api\V1\Director\AccidentsController@show');
        $api->get('accidents', '\App\Http\Controllers\Api\V1\Director\AccidentsController@index');
        $api->get('types', '\App\Http\Controllers\Api\V1\Director\AccidentTypesController@index');
        $api->get('services', '\App\Http\Controllers\Api\V1\Director\DoctorServicesController@index');
        $api->get('assistants', '\App\Http\Controllers\Api\V1\Director\AssistantsController@index');

        $api->get('patients/{id}', '\App\Http\Controllers\Api\V1\Director\PatientsController@show');
        $api->resource('doctors', \App\Http\Controllers\Api\V1\Director\DoctorsController::class);
        $api->resource('cities', \App\Http\Controllers\Api\V1\Director\CitiesController::class);

        $api->get('discounts', '\App\Http\Controllers\Api\V1\Director\DiscountsController@index');
        $api->get('diagnostics', '\App\Http\Controllers\Api\V1\Director\DiagnosticsController@index');

        $api->post('media', '\App\Http\Controllers\Api\V1\Director\MediaController@upload');
        $api->get('media', '\App\Http\Controllers\Api\V1\Director\MediaController@uploads');
        $api->delete('media/{id}', '\App\Http\Controllers\Api\V1\Director\MediaController@destroy');
    });
});
