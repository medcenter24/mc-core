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
        $api->get('accidents/{id}/patient', \App\Http\Controllers\Api\V1\Doctor\AccidentsController::class . '@patient');
        $api->patch('accidents/{id}/patient', \App\Http\Controllers\Api\V1\Doctor\AccidentsController::class . '@updatePatient');
        $api->get('accidents/{id}/status', '\App\Http\Controllers\Api\V1\Doctor\AccidentsController@status');
        $api->get('accidents/{id}/services', '\App\Http\Controllers\Api\V1\Doctor\AccidentsController@services');
        $api->post('accidents/{id}/services', '\App\Http\Controllers\Api\V1\Doctor\AccidentsController@saveService');
        $api->post('accidents/{id}/documents', \App\Http\Controllers\Api\V1\Doctor\AccidentsController::class . '@createDocument');
        $api->get('accidents/{id}/documents', \App\Http\Controllers\Api\V1\Doctor\AccidentsController::class . '@documents');
        $api->get('accidents/{id}/caseType', '\App\Http\Controllers\Api\V1\Doctor\AccidentsController@type');
        $api->get('accidents/{id}/surveys', '\App\Http\Controllers\Api\V1\Doctor\AccidentsController@surveys');
        $api->post('accidents/{id}/surveys', '\App\Http\Controllers\Api\V1\Doctor\AccidentsController@createSurvey');
        $api->get('accidents/{id}/diagnostics', '\App\Http\Controllers\Api\V1\Doctor\AccidentsController@diagnostics');
        $api->post('accidents/{id}/diagnostics', '\App\Http\Controllers\Api\V1\Doctor\AccidentsController@createDiagnostic');
        $api->patch('accidents/{id}/reject', \App\Http\Controllers\Api\V1\Doctor\AccidentsController::class . '@reject');
        $api->resource('accidents', \App\Http\Controllers\Api\V1\Doctor\AccidentsController::class);
        $api->get('me', '\App\Http\Controllers\Api\V1\Doctor\ProfileController@me');
        $api->get('services', '\App\Http\Controllers\Api\V1\Doctor\DoctorServicesController@index');
        $api->get('surveys', '\App\Http\Controllers\Api\V1\Doctor\DoctorSurveysController@index');
        $api->get('diagnostics', '\App\Http\Controllers\Api\V1\Doctor\DiagnosticsController@index');
        $api->get('caseTypes', '\App\Http\Controllers\Api\V1\Doctor\AccidentTypesController@index');
    });

    $api->group(['prefix' => 'director', 'middleware' => ['cors', 'role:director']], function ($api) {

        $api->get('scenario/doctor', \App\Http\Controllers\Api\V1\Director\AccidentScenarioController::class . '@doctorScenario');
        $api->resource('checkpoints', \App\Http\Controllers\Api\V1\Director\AccidentCheckpointsController::class);
        $api->resource('statuses', \App\Http\Controllers\Api\V1\Director\AccidentStatusesController::class);
        $api->resource('users', \App\Http\Controllers\Api\V1\Director\UsersController::class);
        $api->resource('categories', \App\Http\Controllers\Api\V1\Director\CategoriesController::class);

        // Exporter
        $api->post('export/{form}', \App\Http\Controllers\Api\V1\Director\CasesExporterController::class . '@export');

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
        $api->get('cases/{id}/scenario', \App\Http\Controllers\Api\V1\Director\CasesController::class . '@story');
        $api->post('cases/{id}/documents', \App\Http\Controllers\Api\V1\Director\CasesController::class.'@createDocuments');
        $api->get('cases/{id}/documents', \App\Http\Controllers\Api\V1\Director\CasesController::class.'@documents');
        $api->get('cases/{id}/checkpoints', \App\Http\Controllers\Api\V1\Director\CasesController::class.'@getCheckpoints');
        $api->put('cases/{id}/close', \App\Http\Controllers\Api\V1\Director\CasesController::class.'@close');

        $api->resource('cases', \App\Http\Controllers\Api\V1\Director\CasesController::class);

        $api->get('accidents/{id}', '\App\Http\Controllers\Api\V1\Director\AccidentsController@show');
        $api->get('accidents', '\App\Http\Controllers\Api\V1\Director\AccidentsController@index');
        $api->resource('types', \App\Http\Controllers\Api\V1\Director\AccidentTypesController::class);
        $api->resource('services', \App\Http\Controllers\Api\V1\Director\DoctorServicesController::class);
        $api->resource('assistants', \App\Http\Controllers\Api\V1\Director\AssistantsController::class);

        $api->resource('patients', \App\Http\Controllers\Api\V1\Director\PatientsController::class);
        $api->resource('doctors', \App\Http\Controllers\Api\V1\Director\DoctorsController::class);
        $api->get('doctors/{id}/cities', \App\Http\Controllers\Api\V1\Director\DoctorsController::class . '@cities');
        $api->get('doctors/cities/{id}', \App\Http\Controllers\Api\V1\Director\DoctorsController::class . '@getDoctorsByCity');
        $api->put('doctors/{id}/cities', \App\Http\Controllers\Api\V1\Director\DoctorsController::class . '@setCities');
        $api->resource('hospitals', \App\Http\Controllers\Api\V1\Director\HospitalsController::class);
        $api->resource('cities', \App\Http\Controllers\Api\V1\Director\CitiesController::class);

        $api->resource('discounts', \App\Http\Controllers\Api\V1\Director\DiscountsController::class);
        $api->resource('diagnostics', \App\Http\Controllers\Api\V1\Director\DiagnosticsController::class);

        $api->post('media', '\App\Http\Controllers\Api\V1\Director\MediaController@upload');
        $api->get('media', '\App\Http\Controllers\Api\V1\Director\MediaController@uploads');
        $api->delete('media/{id}', '\App\Http\Controllers\Api\V1\Director\MediaController@destroy');

        $api->resource('documents', \App\Http\Controllers\Api\V1\Director\DocumentsController::class);
    });
});
