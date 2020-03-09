<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

use Dingo\Api\Routing\Router;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\AuthenticateController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\AccidentCheckpointsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\AccidentScenarioController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\AccidentStatusesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\AssistantsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseAccidentController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseCaseableController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseCommentController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseDocumentController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseFinanceController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseHistoryController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CasesExporterController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseSourceController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseStatusController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseStoryController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\CategoriesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\CitiesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\CompaniesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\CountriesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\DatePeriodController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\DiseasesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\DoctorsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\FinanceConditionController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\FinanceController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\FinanceCurrencyController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Forms\FormsVariablesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\FormsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\HospitalsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\InvoiceController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\MediaController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\PatientsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\RegionsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\ServicesController as DirectorServicesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Statistics\CalendarController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Statistics\TrafficController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\SurveysController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\UploadsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\UsersController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\AccidentsController as DoctorAccidentsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\AccidentTypesController as DoctorAccidentTypesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\DiagnosticsController as DoctorDiagnosticsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\DoctorServicesController as DoctorDoctorServicesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\DoctorSurveysController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\DocumentsController as DoctorDocumentsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\DocumentsController as DirectorDocumentsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\ProfileController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\AccidentsController as DirectorAccidentsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\AccidentTypesController as DirectorAccidentTypesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\DiagnosticsController as DirectorDiagnosticsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\System\ExtensionsController;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', ['middleware' => 'api'], static function (Router $api) {
    $api->post('authenticate', AuthenticateController::class . '@authenticate');
});
$api->group([
    'version' => 'v1',
    'middleware' => 'api',
    'prefix' => 'api',
], static function (Router $api) {
    $api->version('v1', ['middleware' => ['cors']], static function (Router $api) {
        $api->group([
            'middleware' => 'api.auth'
        ], static function (Router $api) {
            $api->post('logout', AuthenticateController::class . '@logout');
            $api->get('token', AuthenticateController::class . '@getToken');
            $api->get('user', AuthenticateController::class . '@authenticatedUser');
            $api->get('user/company', AuthenticateController::class . '@getCompany');
            $api->get('system/extensions/{extName}', ExtensionsController::class . '@index');

            $api->group(['prefix' => 'doctor', 'middleware' => ['doctor']], static function (Router $api) {
                $api->post('accidents/send', DoctorAccidentsController::class . '@send');
                $api->get('accidents/{id}/patient', DoctorAccidentsController::class . '@patient');
                $api->patch('accidents/{id}/patient', DoctorAccidentsController::class . '@updatePatient');
                $api->get('accidents/{id}/status', DoctorAccidentsController::class . '@status');
                $api->get('accidents/{id}/services', DoctorAccidentsController::class . '@services');
                $api->post('accidents/{id}/services', DoctorAccidentsController::class . '@saveService');
                $api->post('accidents/{id}/documents', DoctorAccidentsController::class . '@createDocument');
                $api->get('accidents/{id}/documents', DoctorAccidentsController::class . '@documents');
                $api->get('accidents/{id}/caseType', DoctorAccidentsController::class . '@type');
                $api->get('accidents/{id}/surveys', DoctorAccidentsController::class . '@surveys');
                $api->post('accidents/{id}/surveys', DoctorAccidentsController::class . '@createSurvey');
                $api->get('accidents/{id}/diagnostics', DoctorAccidentsController::class . '@diagnostics');
                $api->post('accidents/{id}/diagnostics', DoctorAccidentsController::class . '@createDiagnostic');
                $api->patch('accidents/{id}/reject', DoctorAccidentsController::class . '@reject');
                $api->resource('accidents', DoctorAccidentsController::class);
                $api->get('me', ProfileController::class . '@me');
                $api->put('me', ProfileController::class . '@update');
                $api->get('lang/{lang}', ProfileController::class . '@lang');
                $api->get('services', DoctorDoctorServicesController::class . '@index');
                $api->get('surveys', DoctorSurveysController::class . '@index');
                $api->get('diagnostics', DoctorDiagnosticsController::class . '@index');
                $api->get('caseTypes', DoctorAccidentTypesController::class . '@index');
                $api->resource('documents', DoctorDocumentsController::class);
            });

            $api->group(['prefix' => 'director', 'middleware' => ['role:director']], static function (Router $api) {

                $api->resource('uploads', UploadsController::class);

                $api->get('scenario/doctor', AccidentScenarioController::class . '@doctorScenario');

                $api->post('checkpoints/search', AccidentCheckpointsController::class . '@search');
                $api->resource('checkpoints', AccidentCheckpointsController::class);
                $api->post('statuses/search', AccidentStatusesController::class . '@search');
                $api->resource('statuses', AccidentStatusesController::class);

                $api->post('users/search', UsersController::class . '@search');
                $api->resource('users', UsersController::class);
                $api->post('users/{id}/photo', UsersController::class . '@updatePhoto');
                $api->delete('users/{id}/photo', UsersController::class . '@deletePhoto');

                $api->post('categories/search', CategoriesController::class . '@search');
                $api->resource('categories', CategoriesController::class);

                $api->put('companies/{id}', CompaniesController::class . '@update');
                $api->post('companies/{id}/logo', CompaniesController::class . '@uploadLogo');
                $api->post('companies/{id}/sign', CompaniesController::class . '@uploadSign');
                $api->delete('companies/{id}/logo', CompaniesController::class . '@deleteLogo');
                $api->delete('companies/{id}/sign', CompaniesController::class . '@deleteSign');

                // Cases
                $api->group(['prefix' => 'cases'], static function (Router $api) {

                    /** Case Exporter */
                    $api->post('export/{form}', CasesExporterController::class . '@export');

                    /** Case Accident model */
                    $api->post('search', CaseAccidentController::class . '@search');
                    $api->put('{id}', CaseAccidentController::class . '@update');
                    $api->delete('{id}', CaseAccidentController::class . '@destroy');
                    $api->post('', CaseAccidentController::class . '@store');
                    $api->put('{id}', CaseAccidentController::class . '@update');
                    $api->get('{id}', CaseAccidentController::class . '@show');

                    /** Cases Finances */
                    $api->post('{id}/finance', CaseFinanceController::class . '@show');
                    $api->put('{id}/finance/{type}', CaseFinanceController::class . '@save');

                    /** Case Story */
                    $api->get('{id}/scenario', CaseStoryController::class . '@story');

                    /** Caseables */
                    $api->get('{id}/doctorcase', CaseCaseableController::class . '@getDoctorCase');
                    $api->get('{id}/hospitalcase', CaseCaseableController::class . '@getHospitalCase');

                    /** Sources */
                    $api->get('{id}/diagnostics', CaseSourceController::class . '@getDiagnostics');
                    $api->get('{id}/services', CaseSourceController::class . '@getServices');
                    $api->get('{id}/surveys', CaseSourceController::class . '@getSurveys');
                    $api->get('{id}/checkpoints', CaseSourceController::class . '@getCheckpoints');

                    /** Docs */
                    $api->post('{id}/documents', CaseDocumentController::class . '@createDocuments');
                    $api->get('{id}/documents', CaseDocumentController::class . '@documents');

                    /** Case statuses */
                    $api->put('{id}/close', CaseStatusController::class . '@close');

                    /** History */
                    $api->get('{id}/history', CaseHistoryController::class . '@history');

                    /** Commentaries */
                    $api->get('{id}/comments', CaseCommentController::class . '@comments');
                    $api->put('{id}/comments', CaseCommentController::class . '@addComment');
                });

                // todo do I need that?
                $api->post('accidents/search', DirectorAccidentsController::class . '@search');
                $api->get('accidents/{id}', DirectorAccidentsController::class . '@show');
                $api->get('accidents', DirectorAccidentsController::class . '@index');
                $api->resource('types', DirectorAccidentTypesController::class);

                $api->group(['prefix' => 'services'], static function (Router $api) {
                    $api->post('search', DirectorServicesController::class . '@search');
                    $api->delete('{id}', DirectorServicesController::class . '@destroy');
                    $api->get('{id}', DirectorServicesController::class . '@show');
                    $api->post('', DirectorServicesController::class . '@store');
                    $api->put('{id}', DirectorServicesController::class . '@update');
                });

                $api->group(['prefix' => 'surveys'], static function(Router $api) {
                    $api->post('search', SurveysController::class . '@search');
                    $api->delete('{id}', SurveysController::class . '@destroy');
                    $api->get('{id}', SurveysController::class . '@show');
                    $api->post('', SurveysController::class . '@store');
                    $api->put('{id}', SurveysController::class . '@update');
                });


                $api->post('assistants/search', AssistantsController::class . '@search');
                $api->resource('assistants', AssistantsController::class);

                $api->group(['prefix' => 'patients'], static function (Router $api) {
                    $api->post('search', PatientsController::class . '@search');
                    $api->get('{id}', PatientsController::class . '@show');
                    $api->post('', PatientsController::class . '@store');
                    $api->get('', PatientsController::class . '@index');
                    $api->put('{id}', PatientsController::class . '@update');
                    $api->delete('{id}', PatientsController::class . '@destroy');
                });

                $api->resource('doctors', DoctorsController::class);
                $api->group(['prefix' => 'doctors'], function (Router $api) {
                    $api->get('{id}/cities', DoctorsController::class . '@cities');
                    $api->put('{id}/cities', DoctorsController::class . '@setCities');
                    $api->post('search', DoctorsController::class . '@search');
                    $api->get('cities/{id}', DoctorsController::class . '@getDoctorsByCity');
                });

                $api->post('hospitals/search', HospitalsController::class . '@search');
                $api->resource('hospitals', HospitalsController::class);

                $api->post('cities/search', CitiesController::class . '@search');
                $api->resource('cities', CitiesController::class);

                $api->resource('countries', CountriesController::class);
                $api->group(['prefix' => 'countries'], static function(Router $api) {
                    $api->post('search', CountriesController::class . '@search');
                });

                $api->resource('regions', RegionsController::class);
                $api->group(['prefix' => 'regions'], static function(Router $api) {
                    $api->post('search', RegionsController::class . '@search');
                });

                $api->group(['prefix' => 'diagnostics'], static function(Router $api) {
                    $api->post('search', DirectorDiagnosticsController::class . '@search');
                    $api->delete('{id}', DirectorDiagnosticsController::class . '@destroy');
                    $api->get('{id}', DirectorDiagnosticsController::class . '@show');
                    $api->post('', DirectorDiagnosticsController::class . '@store');
                    $api->put('{id}', DirectorDiagnosticsController::class . '@update');
                });

                $api->post('media', MediaController::class . '@upload');
                $api->get('media', MediaController::class . '@uploads');
                $api->delete('media/{id}', MediaController::class . '@destroy');

                $api->resource('documents', DirectorDocumentsController::class);

                $api->group(['prefix' => 'statistics'], function (Router $api) {
                    $api->get('calendar', CalendarController::class . '@index');
                    $api->get('doctorsTraffic', TrafficController::class . '@doctors');
                    $api->get('assistantsTraffic', TrafficController::class . '@assistants');
                    $api->get('years', TrafficController::class . '@years');
                });

                $api->group(['prefix' => 'finance'], static function (Router $api) {
                    $api->post('search', FinanceConditionController::class . '@search');
                    $api->get('{id}', FinanceConditionController::class . '@show');
                    $api->delete('{id}', FinanceConditionController::class . '@destroy');
                    $api->post('', FinanceController::class . '@store');
                    $api->put('{id}', FinanceController::class . '@update');
                });

                $api->post('currency/search', FinanceCurrencyController::class . '@search');
                $api->resource('currency', FinanceCurrencyController::class);

                $api->post('periods/search', DatePeriodController::class . '@search');
                $api->resource('periods', DatePeriodController::class);

                $api->group(['prefix' => 'forms'], static function (Router $api) {
                    $api->post('search', FormsController::class . '@search');
                    $api->group(['prefix' => 'variables'], static function (Router $api) {
                        $api->post('search', FormsVariablesController::class . '@search');
                    });
                    $api->get('', FormsController::class . '@index');
                    $api->get('/{id}', FormsController::class . '@show');
                    $api->post('/', FormsController::class . '@store');
                    $api->put('/{id}', FormsController::class . '@update');
                    $api->delete('/{id}', FormsController::class . '@destroy');
                    $api->get('/{formId}/{srcId}/pdf', FormsController::class . '@pdf');
                    $api->get('/{formId}/{srcId}/html', FormsController::class . '@html');
                });

                $api->post('invoice/search', InvoiceController::class . '@search');
                $api->get('invoice/{id}/form', InvoiceController::class . '@form');
                $api->get('invoice/{id}/file', InvoiceController::class . '@file');
                $api->resource('invoice', InvoiceController::class);

                $api->post('diseases/search', DiseasesController::class . '@search');
                $api->resource('diseases', DiseasesController::class);
            });
        });
    });
});
