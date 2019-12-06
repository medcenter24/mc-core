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

use Dingo\Api\Routing\Router;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\AuthenticateController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\AccidentCheckpointsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\AccidentScenarioController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\AccidentStatusesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\AssistantsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseFinanceController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\CasesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\CasesExporterController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\CategoriesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\CitiesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\CompaniesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\CountriesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\DatePeriodController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\DoctorsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\FinanceController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\FinanceCurrencyController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Forms\FormsVariablesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\FormsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\HospitalsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\InvoiceController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\MediaController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\PatientsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\PaymentController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\RegionsController;
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
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\DoctorServicesController as DirectorDoctorServicesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\DiagnosticsController as DirectorDiagnosticsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\System\ExtensionsController;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', ['middleware' => 'api'], static function ($api) {
    $api->post('authenticate', AuthenticateController::class . '@authenticate');
});
$api->group([
    'version' => 'v1',
    'middleware' => 'api',
    'prefix' => 'api',
], static function ($api) {
    $api->version('v1', ['middleware' => ['cors']], static function ($api) {
        $api->group([
            'middleware' => 'api.auth'
        ], static function ($api) {
            $api->post('logout', AuthenticateController::class . '@logout');
            $api->get('token', AuthenticateController::class . '@getToken');
            $api->get('user', AuthenticateController::class . '@authenticatedUser');
            $api->get('user/company', AuthenticateController::class . '@getCompany');
            $api->get('system/extensions/{extName}', ExtensionsController::class . '@index');

            $api->group(['prefix' => 'doctor', 'middleware' => ['role:doctor']], static function ($api) {
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
                $api->get('lang/{lang}', ProfileController::class . '@lang');
                $api->get('services', DoctorDoctorServicesController::class . '@index');
                $api->get('surveys', DoctorSurveysController::class . '@index');
                $api->get('diagnostics', DoctorDiagnosticsController::class . '@index');
                $api->get('caseTypes', DoctorAccidentTypesController::class . '@index');
                $api->resource('documents', DoctorDocumentsController::class);
            });

            $api->group(['prefix' => 'director', 'middleware' => ['role:director']], static function ($api) {

                $api->resource('uploads', UploadsController::class);

                $api->get('scenario/doctor', AccidentScenarioController::class . '@doctorScenario');

                $api->post('checkpoints/search', AccidentCheckpointsController::class . '@search');
                $api->resource('checkpoints', AccidentCheckpointsController::class);
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
                $api->group(['prefix' => 'cases'], static function ($api) {

                    $api->post('export/{form}', CasesExporterController::class . '@export');

                    // assigned to case
                    $api->post('search', CasesController::class . '@search');
                    $api->get('{id}/scenario', CasesController::class . '@story');
                    $api->get('{id}/doctorcase', CasesController::class . '@getDoctorCase');
                    $api->get('{id}/hospitalcase', CasesController::class . '@getHospitalCase');
                    $api->get('{id}/diagnostics', CasesController::class . '@getDiagnostics');
                    $api->get('{id}/services', CasesController::class . '@getServices');
                    $api->get('{id}/surveys', CasesController::class . '@getSurveys');
                    $api->post('{id}/documents', CasesController::class . '@createDocuments');
                    $api->get('{id}/documents', CasesController::class . '@documents');
                    $api->get('{id}/checkpoints', CasesController::class . '@getCheckpoints');
                    $api->put('{id}/close', CasesController::class . '@close');
                    $api->get('{id}/history', CasesController::class . '@history');
                    $api->get('{id}/comments', CasesController::class . '@comments');
                    $api->put('{id}/comments', CasesController::class . '@addComment');
                    $api->post('{id}/finance', CaseFinanceController::class . '@show');
                    $api->put('{id}/finance/{type}', CaseFinanceController::class . '@save');
                    $api->put('{id}', CasesController::class . '@update');
                    $api->delete('{id}', CasesController::class . '@destroy');
                    $api->post('', CasesController::class . '@store');
                    $api->put('{id}', CasesController::class . '@update');
                });

                $api->post('accidents/search', DirectorAccidentsController::class . '@search');
                $api->get('accidents/{id}', DirectorAccidentsController::class . '@show');
                $api->get('accidents', DirectorAccidentsController::class . '@index');
                $api->resource('types', DirectorAccidentTypesController::class);

                $api->post('services/search', DirectorDoctorServicesController::class . '@search');
                // $api->post('services', DirectorDoctorServicesController::class . '@store');
                $api->resource('services', DirectorDoctorServicesController::class);

                $api->post('surveys/search', SurveysController::class . '@search');
                $api->resource('surveys', SurveysController::class);

                $api->post('assistants/search', AssistantsController::class . '@search');
                $api->resource('assistants', AssistantsController::class);

                $api->group(['prefix' => 'patients'], static function ($api) {
                    $api->post('search', PatientsController::class . '@search');
                    $api->get('{id}', PatientsController::class . '@show');
                    $api->post('', PatientsController::class . '@store');
                    $api->get('', PatientsController::class . '@index');
                    $api->put('{id}', PatientsController::class . '@update');
                    $api->delete('{id}', PatientsController::class . '@destroy');
                });

                $api->resource('doctors', DoctorsController::class);
                $api->group(['prefix' => 'doctors'], function ($api) {
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
                $api->group(['prefix' => 'countries'], static function($api) {
                    $api->post('search', CountriesController::class . '@search');
                });

                $api->resource('regions', RegionsController::class);
                $api->group(['prefix' => 'regions'], static function($api) {
                    $api->post('search', RegionsController::class . '@search');
                });

                $api->post('diagnostics/search', DirectorDiagnosticsController::class . '@search');
                $api->resource('diagnostics', DirectorDiagnosticsController::class);

                $api->post('media', MediaController::class . '@upload');
                $api->get('media', MediaController::class . '@uploads');
                $api->delete('media/{id}', MediaController::class . '@destroy');

                $api->resource('documents', DirectorDocumentsController::class);

                $api->group(['prefix' => 'statistics'], function ($api) {
                    $api->get('calendar', CalendarController::class . '@index');
                    $api->get('doctorsTraffic', TrafficController::class . '@doctors');
                    $api->get('assistantsTraffic', TrafficController::class . '@assistants');
                    $api->get('years', TrafficController::class . '@years');
                });

                $api->post('finance/search', FinanceController::class . '@search');
                $api->resource('finance', FinanceController::class);

                $api->resource('payment', PaymentController::class);

                $api->post('currency/search', FinanceCurrencyController::class . '@search');
                $api->resource('currency', FinanceCurrencyController::class);

                $api->post('periods/search', DatePeriodController::class . '@search');
                $api->resource('periods', DatePeriodController::class);

                $api->group(['prefix' => 'forms'], static function ($api) {
                    $api->post('search', FormsController::class . '@search');
                    $api->group(['prefix' => 'variables'], static function ($api) {
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

                // @TODO do not use such records at all, we need to create group then all references of this group

                $api->post('invoice/search', InvoiceController::class . '@search');
                $api->get('invoice/{id}/form', InvoiceController::class . '@form');
                $api->get('invoice/{id}/file', InvoiceController::class . '@file');
                $api->resource('invoice', InvoiceController::class);
            });
        });
    });
});
