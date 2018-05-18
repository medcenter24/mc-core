<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

/** @var \Dingo\Api\Routing\Router $api */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['middleware' => 'api'], function ($api) {
    $api->post('authenticate', \App\Http\Controllers\Api\V1\AuthenticateController::class . '@authenticate');
});
$api->group([
    'version' => 'v1',
    'middleware' => 'api',
    'prefix' => 'api',
], function ($api) {
    $api->version('v1', ['middleware' => ['cors']], function ($api) {
        $api->group([
            'middleware' => 'api.auth'
        ], function ($api) {
            $api->post('logout', '\App\Http\Controllers\Api\V1\AuthenticateController@logout');
            $api->get('token', \App\Http\Controllers\Api\V1\AuthenticateController::class . '@getToken');
            $api->get('user', \App\Http\Controllers\Api\V1\AuthenticateController::class . '@authenticatedUser');
            $api->get('user/company', \App\Http\Controllers\Api\V1\AuthenticateController::class . '@getCompany');

            $api->group(['prefix' => 'doctor', 'middleware' => ['role:doctor']], function ($api) {
                $api->post('accidents/send', \App\Http\Controllers\Api\V1\Doctor\AccidentsController::class . '@send');
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
                $api->get('me', \App\Http\Controllers\Api\V1\Doctor\ProfileController::class . '@me');
                $api->get('services', '\App\Http\Controllers\Api\V1\Doctor\DoctorServicesController@index');
                $api->get('surveys', '\App\Http\Controllers\Api\V1\Doctor\DoctorSurveysController@index');
                $api->get('diagnostics', '\App\Http\Controllers\Api\V1\Doctor\DiagnosticsController@index');
                $api->get('caseTypes', '\App\Http\Controllers\Api\V1\Doctor\AccidentTypesController@index');
                $api->resource('documents', \App\Http\Controllers\Api\V1\Director\DocumentsController::class);
                $api->get('lang/{lang}', \App\Http\Controllers\Api\V1\Doctor\ProfileController::class . '@lang');
            });

            $api->group(['prefix' => 'director', 'middleware' => ['role:director']], function ($api) {
                $api->get('scenario/doctor', \App\Http\Controllers\Api\V1\Director\AccidentScenarioController::class . '@doctorScenario');

                $api->post('checkpoints/search', \App\Http\Controllers\Api\V1\Director\AccidentCheckpointsController::class . '@search');
                $api->resource('checkpoints', \App\Http\Controllers\Api\V1\Director\AccidentCheckpointsController::class);
                $api->resource('statuses', \App\Http\Controllers\Api\V1\Director\AccidentStatusesController::class);

                $api->post('users/search', \App\Http\Controllers\Api\V1\Director\UsersController::class . '@search');
                $api->resource('users', \App\Http\Controllers\Api\V1\Director\UsersController::class);

                $api->post('users/{id}/photo', \App\Http\Controllers\Api\V1\Director\UsersController::class . '@updatePhoto');
                $api->delete('users/{id}/photo', \App\Http\Controllers\Api\V1\Director\UsersController::class . '@deletePhoto');

                $api->post('categories/search', \App\Http\Controllers\Api\V1\Director\CategoriesController::class . '@search');
                $api->resource('categories', \App\Http\Controllers\Api\V1\Director\CategoriesController::class);

                $api->put('companies/{id}', \App\Http\Controllers\Api\V1\Director\CompaniesController::class . '@update');
                $api->post('companies/{id}/logo', \App\Http\Controllers\Api\V1\Director\CompaniesController::class . '@uploadLogo');
                $api->post('companies/{id}/sign', \App\Http\Controllers\Api\V1\Director\CompaniesController::class . '@uploadSign');
                $api->delete('companies/{id}/logo', \App\Http\Controllers\Api\V1\Director\CompaniesController::class . '@deleteLogo');
                $api->delete('companies/{id}/sign', \App\Http\Controllers\Api\V1\Director\CompaniesController::class . '@deleteSign');

                // Exporter
                // todo move exporter to the cases group
                $api->post('export/{form}', \App\Http\Controllers\Api\V1\Director\CasesExporterController::class . '@export');

                // Cases
                $api->group(['prefix' => 'cases'], function ($api) {

                    $api->group(['prefix' => 'importer'], function ($api) {
                        $api->post('', \App\Http\Controllers\Api\V1\Director\CasesImporterController::class . '@upload');
                        $api->get('', \App\Http\Controllers\Api\V1\Director\CasesImporterController::class . '@uploads');
                        $api->put('{id}', \App\Http\Controllers\Api\V1\Director\CasesImporterController::class . '@import');
                        $api->delete('{id}', \App\Http\Controllers\Api\V1\Director\CasesImporterController::class . '@destroy');
                    });

                    // assigned to case
                    $api->post('search', \App\Http\Controllers\Api\V1\Director\CasesController::class . '@search');
                    $api->get('{id}/doctorcase', '\App\Http\Controllers\Api\V1\Director\CasesController@getDoctorCase');
                    $api->get('{id}/hospitalcase', '\App\Http\Controllers\Api\V1\Director\CasesController@getHospitalCase');
                    $api->get('{id}/diagnostics', '\App\Http\Controllers\Api\V1\Director\CasesController@getDiagnostics');
                    $api->get('{id}/services', \App\Http\Controllers\Api\V1\Director\CasesController::class . '@getServices');
                    $api->get('{id}/surveys', \App\Http\Controllers\Api\V1\Director\CasesController::class . '@getSurveys');
                    $api->get('{id}/scenario', \App\Http\Controllers\Api\V1\Director\CasesController::class . '@story');
                    $api->post('{id}/documents', \App\Http\Controllers\Api\V1\Director\CasesController::class.'@createDocuments');
                    $api->get('{id}/documents', \App\Http\Controllers\Api\V1\Director\CasesController::class.'@documents');
                    $api->get('{id}/checkpoints', \App\Http\Controllers\Api\V1\Director\CasesController::class.'@getCheckpoints');
                    $api->put('{id}/close', \App\Http\Controllers\Api\V1\Director\CasesController::class.'@close');
                    $api->get('{id}/reportHtml', \App\Http\Controllers\Api\V1\Director\CasesController::class . '@reportHtml');
                    $api->get('{id}/downloadPdf', \App\Http\Controllers\Api\V1\Director\CasesController::class . '@downloadPdf');
                    $api->get('{id}/history', \App\Http\Controllers\Api\V1\Director\CasesController::class . '@history');
                    $api->get('{id}/comments', \App\Http\Controllers\Api\V1\Director\CasesController::class . '@comments');
                    $api->put('{id}/comments', \App\Http\Controllers\Api\V1\Director\CasesController::class . '@addComment');
                    $api->resource('', \App\Http\Controllers\Api\V1\Director\CasesController::class);
                });

                $api->get('accidents/{id}', '\App\Http\Controllers\Api\V1\Director\AccidentsController@show');
                $api->get('accidents', '\App\Http\Controllers\Api\V1\Director\AccidentsController@index');
                $api->resource('types', \App\Http\Controllers\Api\V1\Director\AccidentTypesController::class);

                $api->post('services/search', \App\Http\Controllers\Api\V1\Director\DoctorServicesController::class . '@search');
                $api->resource('services', \App\Http\Controllers\Api\V1\Director\DoctorServicesController::class);
                $api->resource('surveys', \App\Http\Controllers\Api\V1\Director\SurveysController::class);

                $api->post('assistants/search', \App\Http\Controllers\Api\V1\Director\AssistantsController::class . '@search');
                $api->resource('assistants', \App\Http\Controllers\Api\V1\Director\AssistantsController::class);

                $api->post('patients/search', \App\Http\Controllers\Api\V1\Director\PatientsController::class . '@search');
                $api->resource('patients', \App\Http\Controllers\Api\V1\Director\PatientsController::class);

                $api->resource('doctors', \App\Http\Controllers\Api\V1\Director\DoctorsController::class);
                $api->group(['prefix' => 'doctors'], function ($api) {
                    $api->get('{id}/cities', \App\Http\Controllers\Api\V1\Director\DoctorsController::class . '@cities');
                    $api->put('{id}/cities', \App\Http\Controllers\Api\V1\Director\DoctorsController::class . '@setCities');
                    $api->post('search', \App\Http\Controllers\Api\V1\Director\DoctorsController::class . '@search');
                    $api->get('cities/{id}', \App\Http\Controllers\Api\V1\Director\DoctorsController::class . '@getDoctorsByCity');
                });

                $api->post('hospitals/search', \App\Http\Controllers\Api\V1\Director\HospitalsController::class . '@search');
                $api->resource('hospitals', \App\Http\Controllers\Api\V1\Director\HospitalsController::class);

                $api->post('cities/search', \App\Http\Controllers\Api\V1\Director\CitiesController::class . '@search');
                $api->resource('cities', \App\Http\Controllers\Api\V1\Director\CitiesController::class);

                $api->post('diagnostics/search', \App\Http\Controllers\Api\V1\Director\DiagnosticsController::class . '@search');
                $api->resource('diagnostics', \App\Http\Controllers\Api\V1\Director\DiagnosticsController::class);

                $api->post('media', '\App\Http\Controllers\Api\V1\Director\MediaController@upload');
                $api->get('media', '\App\Http\Controllers\Api\V1\Director\MediaController@uploads');
                $api->delete('media/{id}', '\App\Http\Controllers\Api\V1\Director\MediaController@destroy');

                $api->resource('documents', \App\Http\Controllers\Api\V1\Director\DocumentsController::class);

                $api->group(['prefix' => 'statistics'], function ($api) {
                    $api->get('calendar', \App\Http\Controllers\Api\V1\Director\Statistics\CalendarController::class . '@index');
                    $api->get('doctorsTraffic', \App\Http\Controllers\Api\V1\Director\Statistics\TrafficController::class . '@doctors');
                    $api->get('assistantsTraffic', \App\Http\Controllers\Api\V1\Director\Statistics\TrafficController::class . '@assistants');
                });

                $api->get('finance', \App\Http\Controllers\Api\V1\Director\FinanceController::class . '@index');
                $api->post('finance', \App\Http\Controllers\Api\V1\Director\FinanceController::class . '@store');
                $api->get('finance/{id}', \App\Http\Controllers\Api\V1\Director\FinanceController::class . '@show');
                $api->put('finance/{id}', \App\Http\Controllers\Api\V1\Director\FinanceController::class . '@update');
                $api->delete('finance/{id}', \App\Http\Controllers\Api\V1\Director\FinanceController::class . '@destroy');

                $api->post('periods/search', \App\Http\Controllers\Api\V1\Director\DatePeriodController::class . '@search');
                $api->resource('periods', \App\Http\Controllers\Api\V1\Director\DatePeriodController::class);
            });
        });
    });
});
