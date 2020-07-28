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
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\AccidentsController as DirectorAccidentsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\AccidentStatusesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\AccidentTypesController as DirectorAccidentTypesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\AssistantsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseAccidentController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseCaseableController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseCommentController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseDocumentController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseExporterController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseFinanceController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseHistoryController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseSourceController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseStatusController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases\CaseStoryController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\DiagnosticsCategoriesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\CitiesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\CompaniesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\CountriesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\DatePeriodController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\DiseasesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\DoctorsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\FinanceConditionController;
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
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\Accident\CaseTypeController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\AccidentTypesController as DoctorAccidentTypesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\Accident\DiagnosticsAccidentController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\DiagnosticsController as DoctorDiagnosticsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\Accident\DoctorCaseController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\ServicesController as DoctorServicesController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\SurveysController as DoctorSurveysController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\Accident\DocumentsAccidentController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\DocumentsController as DoctorDocumentsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\DocumentsController as DirectorDocumentsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\Accident\PatientAccidentController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\ProfileController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\DiagnosticsController as DirectorDiagnosticsController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\Accident\ServicesAccidentController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\Accident\StatusAccidentController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\Accident\SurveysAccidentController;
use medcenter24\mcCore\App\Http\Controllers\Api\V1\System\ExtensionsController;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', ['middleware' => ['api']], static function (Router $api) {
    $api->post('authenticate', AuthenticateController::class . '@authenticate');
});
$api->group([
    'version' => 'v1',
    'middleware' => 'api',
    'prefix' => 'api',
], static function (Router $api) {
    $api->version('v1', ['middleware' => ['cors', 'api.auth']], static function (Router $api) {
        $api->post('logout', AuthenticateController::class . '@logout');
        $api->get('token', AuthenticateController::class . '@getToken');
        $api->get('user', AuthenticateController::class . '@authenticatedUser');
        $api->get('user/company', AuthenticateController::class . '@getCompany');
        $api->get('system/extensions/{extName}', ExtensionsController::class . '@index');

        $api->group(['prefix' => 'doctor', 'middleware' => ['doctor', 'role:login']], static function (Router $api) {

            $api->group(['prefix' => 'accidents'], static function (Router $api) {
                $api->get('{id}/patient', PatientAccidentController::class . '@patient');
                $api->put('{id}/patient', PatientAccidentController::class . '@updatePatient');

                $api->get('{id}/status', StatusAccidentController::class . '@status');
                $api->post('{id}/reject', StatusAccidentController::class . '@reject');
                $api->post('send', StatusAccidentController::class . '@send');

                $api->get('{id}/services', ServicesAccidentController::class . '@services');
                $api->post('{id}/services', ServicesAccidentController::class . '@saveService');

                $api->post('{id}/documents', DocumentsAccidentController::class . '@createDocument');
                $api->get('{id}/documents', DocumentsAccidentController::class . '@documents');

                $api->get('{id}/surveys', SurveysAccidentController::class . '@surveys');
                $api->post('{id}/surveys', SurveysAccidentController::class . '@createSurvey');

                $api->get('{id}/diagnostics', DiagnosticsAccidentController::class . '@diagnostics');
                $api->post('{id}/diagnostics', DiagnosticsAccidentController::class . '@saveDiagnostic');

                $api->get('', DoctorCaseController::class . '@index');
                $api->get('{id}/caseType', CaseTypeController::class . '@show');
                $api->get('{id}', DoctorCaseController::class . '@show');
                $api->post('{id}', DoctorCaseController::class . '@update');
            });

            $api->get('me', ProfileController::class . '@me');
            $api->put('me', ProfileController::class . '@update');

            $api->get('lang/{lang}', ProfileController::class . '@lang');

            $api->get('services', DoctorServicesController::class . '@index');
            $api->get('surveys', DoctorSurveysController::class . '@index');
            $api->get('diagnostics', DoctorDiagnosticsController::class . '@index');

            $api->get('caseTypes', DoctorAccidentTypesController::class . '@index');

            $api->group(['prefix' => 'documents'], static function (Router $api) {
                $api->get('{id}', DoctorDocumentsController::class . '@show');
                $api->delete('{id}', DoctorDocumentsController::class . '@destroy');
                $api->patch('{id}', DoctorDocumentsController::class . '@update');
            });
        });

        $api->group(['prefix' => 'director', 'middleware' => ['role:director', 'role:login']], static function (Router $api) {

            // Secure file uploader
            # uses for invoices, forms, etc.
            $api->group(['prefix' => 'uploads'], static function (Router $api) {
                $api->post('', UploadsController::class . '@store');
                $api->get('{id}', UploadsController::class . '@show');
            });

            $api->group(['prefix' => 'checkpoints'], static function (Router $api) {
                $api->post('search', AccidentCheckpointsController::class . '@search');
                $api->get('{id}', AccidentCheckpointsController::class . '@show');
                $api->post('', AccidentCheckpointsController::class . '@store');
                $api->put('{id}', AccidentCheckpointsController::class . '@update');
                $api->delete('{id}', AccidentCheckpointsController::class . '@destroy');
            });

            $api->group(['prefix' => 'statuses'], static function (Router $api) {
                $api->post('search', AccidentStatusesController::class . '@search');
                $api->get('{id}', AccidentStatusesController::class . '@show');
                $api->post('', AccidentStatusesController::class . '@store');
                $api->put('{id}', AccidentStatusesController::class . '@update');
                $api->delete('{id}', AccidentStatusesController::class . '@destroy');
            });

            $api->group(['prefix' => 'users'], static function (Router $api) {
                $api->post('search', UsersController::class . '@search');
                $api->get('{id}', UsersController::class . '@show');
                $api->post('', UsersController::class . '@store');
                $api->put('{id}', UsersController::class . '@update');
                $api->delete('{id}', UsersController::class . '@destroy');

                // photo
                $api->post('{id}/photo', UsersController::class . '@updatePhoto');
                $api->delete('{id}/photo', UsersController::class . '@deletePhoto');
            });

            $api->group(['prefix' => 'companies'], static function (Router $api) {
                $api->put('{id}', CompaniesController::class . '@update');
                $api->post('{id}/logo', CompaniesController::class . '@uploadLogo');
                $api->delete('{id}/logo', CompaniesController::class . '@deleteLogo');
            });

            // Cases
            $api->group(['prefix' => 'cases'], static function (Router $api) {

                /** Exporter (same mechanism for exporters: binary response) */
                $api->post('export', CaseExporterController::class . '@export');

                /** Case Accident model */
                $api->post('search', CaseAccidentController::class . '@search');
                $api->put('{id}', CaseAccidentController::class . '@update');
                $api->delete('{id}', CaseAccidentController::class . '@destroy');
                $api->post('', CaseAccidentController::class . '@store');
                $api->get('{id}', CaseAccidentController::class . '@show');

                /** Cases Finances */
                $api->get('{id}/finance', CaseFinanceController::class . '@show');
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
                $api->post('{id}/comments', CaseCommentController::class . '@addComment');
            });

            // Director can assign accident to another parent accident
            $api->group(['prefix' => 'accidents'], static function (Router $api) {
                // list of accidents
               $api->post('search', DirectorAccidentsController::class . '@search');
               // selected case
               $api->get('{id}', DirectorAccidentsController::class . '@show');
            });

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

            $api->group(['prefix' => 'assistants'], static function(Router $api) {
                $api->post('search', AssistantsController::class . '@search');
                $api->delete('{id}', AssistantsController::class . '@destroy');
                $api->get('{id}', AssistantsController::class . '@show');
                $api->post('', AssistantsController::class . '@store');
                $api->put('{id}', AssistantsController::class . '@update');
            });

            $api->group(['prefix' => 'patients'], static function (Router $api) {
                $api->post('search', PatientsController::class . '@search');
                $api->get('{id}', PatientsController::class . '@show');
                $api->post('', PatientsController::class . '@store');
                $api->get('', PatientsController::class . '@index');
                $api->put('{id}', PatientsController::class . '@update');
                $api->delete('{id}', PatientsController::class . '@destroy');
            });

            $api->group(['prefix' => 'doctors'], function (Router $api) {
                $api->post('search', DoctorsController::class . '@search');
                $api->get('{id}', DoctorsController::class . '@show');
                $api->post('', DoctorsController::class . '@store');
                $api->get('', DoctorsController::class . '@index');
                $api->put('{id}', DoctorsController::class . '@update');
                $api->delete('{id}', DoctorsController::class . '@destroy');

                $api->get('{id}/cities', DoctorsController::class . '@cities');
                $api->put('{id}/cities', DoctorsController::class . '@setCities');
                $api->get('cities/{id}', DoctorsController::class . '@getDoctorsByCity');
            });

            $api->group(['prefix' => 'hospitals'], static function (Router $api) {
                $api->post('search', HospitalsController::class . '@search');
                $api->get('{id}', HospitalsController::class . '@show');
                $api->post('', HospitalsController::class . '@store');
                $api->get('', HospitalsController::class . '@index');
                $api->put('{id}', HospitalsController::class . '@update');
                $api->delete('{id}', HospitalsController::class . '@destroy');
            });

            $api->group(['prefix' => 'cities'], static function (Router $api) {
                $api->post('search', CitiesController::class . '@search');
                $api->get('{id}', CitiesController::class . '@show');
                $api->post('', CitiesController::class . '@store');
                $api->get('', CitiesController::class . '@index');
                $api->put('{id}', CitiesController::class . '@update');
                $api->delete('{id}', CitiesController::class . '@destroy');
            });

            $api->group(['prefix' => 'countries'], static function (Router $api) {
                $api->post('search', CountriesController::class . '@search');
                $api->get('{id}', CountriesController::class . '@show');
                $api->post('', CountriesController::class . '@store');
                $api->get('', CountriesController::class . '@index');
                $api->put('{id}', CountriesController::class . '@update');
                $api->delete('{id}', CountriesController::class . '@destroy');
            });

            $api->group(['prefix' => 'regions'], static function (Router $api) {
                $api->post('search', RegionsController::class . '@search');
                $api->get('{id}', RegionsController::class . '@show');
                $api->post('', RegionsController::class . '@store');
                $api->get('', RegionsController::class . '@index');
                $api->put('{id}', RegionsController::class . '@update');
                $api->delete('{id}', RegionsController::class . '@destroy');
            });

            $api->group(['prefix' => 'diagnostics'], static function(Router $api) {
                $api->post('search', DirectorDiagnosticsController::class . '@search');
                $api->delete('{id}', DirectorDiagnosticsController::class . '@destroy');
                $api->get('{id}', DirectorDiagnosticsController::class . '@show');
                $api->post('', DirectorDiagnosticsController::class . '@store');
                $api->put('{id}', DirectorDiagnosticsController::class . '@update');

                $api->group(['prefix' => 'categories'], static function (Router $api) {
                    $api->post('search', DiagnosticsCategoriesController::class . '@search');
                    $api->get('{id}', DiagnosticsCategoriesController::class . '@show');
                    $api->post('', DiagnosticsCategoriesController::class . '@store');
                    $api->put('{id}', DiagnosticsCategoriesController::class . '@update');
                    $api->delete('{id}', DiagnosticsCategoriesController::class . '@destroy');
                });
            });

            $api->group(['prefix' => 'media'], static function(Router $api) {
                $api->post('', MediaController::class . '@upload');
                $api->get('', MediaController::class . '@uploads');
                $api->delete('{id}', MediaController::class . '@destroy');
            });

            $api->group(['prefix' => 'documents'], static function(Router $api) {
                $api->get('{id}', DirectorDocumentsController::class . '@show');
                $api->delete('{id}', DirectorDocumentsController::class . '@destroy');
                $api->put('{id}', DirectorDocumentsController::class . '@update');
            });

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
                $api->post('', FinanceConditionController::class . '@store');
                $api->put('{id}', FinanceConditionController::class . '@update');
            });

            $api->group(['prefix' => 'currency'], static function (Router $api) {
                $api->post('search', FinanceCurrencyController::class . '@search');
                $api->get('{id}', FinanceCurrencyController::class . '@show');
                $api->post('', FinanceCurrencyController::class . '@store');
                $api->put('{id}', FinanceCurrencyController::class . '@update');
                $api->delete('{id}', FinanceCurrencyController::class . '@destroy');
            });

            $api->group(['prefix' => 'periods'], static function (Router $api) {
                $api->post('search', DatePeriodController::class . '@search');
                $api->get('{id}', DatePeriodController::class . '@show');
                $api->post('', DatePeriodController::class . '@store');
                $api->get('', DatePeriodController::class . '@index');
                $api->put('{id}', DatePeriodController::class . '@update');
                $api->delete('{id}', DatePeriodController::class . '@destroy');
            });

            $api->group(['prefix' => 'forms'], static function (Router $api) {
                $api->post('search', FormsController::class . '@search');
                $api->group(['prefix' => 'variables'], static function (Router $api) {
                    $api->post('search', FormsVariablesController::class . '@search');
                });
                $api->get('', FormsController::class . '@index');
                $api->get('{id}', FormsController::class . '@show');
                $api->post('/', FormsController::class . '@store');
                $api->put('{id}', FormsController::class . '@update');
                $api->delete('{id}', FormsController::class . '@destroy');
                $api->get('/{formId}/{srcId}/pdf', FormsController::class . '@pdf');
                $api->get('/{formId}/{srcId}/html', FormsController::class . '@html');
            });

            $api->group(['prefix' => 'invoice'], static function (Router $api) {
                $api->post('search', InvoiceController::class . '@search');
                $api->get('{id}', InvoiceController::class . '@show');
                $api->post('', InvoiceController::class . '@store');
                $api->get('', InvoiceController::class . '@index');
                $api->put('{id}', InvoiceController::class . '@update');
                $api->delete('{id}', InvoiceController::class . '@destroy');

                $api->get('{id}/form', InvoiceController::class . '@form');
                $api->get('{id}/file', InvoiceController::class . '@file');
            });

            $api->group(['prefix' => 'diseases'], static function (Router $api) {
                $api->post('search', DiseasesController::class . '@search');
                $api->get('{id}', DiseasesController::class . '@show');
                $api->post('', DiseasesController::class . '@store');
                $api->get('', DiseasesController::class . '@index');
                $api->put('{id}', DiseasesController::class . '@update');
                $api->delete('{id}', DiseasesController::class . '@destroy');
            });
        });
    });
});
