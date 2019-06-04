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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');

// if it needed I could check that server is telegram
Route::group(['prefix' => 'telegram'], function () {
    Route::post(env('TELEGRAM_WEBHOOK_PREFIX'), 'Telegram\TelegramApiController@index');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'role:admin']], function () {
    Route::get('/', 'Admin\MainController@index');
    Route::resource('users', 'Admin\UsersController');
    Route::resource('roles', 'Admin\RolesController');
    Route::resource('invites', 'Admin\InvitesController');

    Route::group(['prefix' => 'preview'], function() {
        Route::get('caseReport', 'Admin\PreviewController@caseReport');
        Route::get('caseHistory', 'Admin\PreviewController@caseHistory');
        Route::get('messenger', 'Admin\PreviewController@messenger');
        Route::get('telegram', 'Admin\PreviewController@telegram');
    });

    Route::group(['prefix' => 'cases'], function () {
        Route::get('/', 'Admin\CasesController@search');
        Route::get('report', 'Admin\CasesController@report');
        Route::get('pdf', 'Admin\CasesController@downloadPdf');
        Route::get('history', 'Admin\CasesController@history');
    });

    Route::group(['prefix' => 'messenger'], function () {
        Route::group(['prefix' => 'thread'], function () {
            Route::get('/', 'Admin\Messenger\ThreadController@index');
            Route::get('/counts', 'Admin\Messenger\ThreadController@counts');
            Route::get('/{id}', 'Admin\Messenger\ThreadController@show');
            Route::post('/', 'Admin\Messenger\ThreadController@create');
            Route::post('/{id}/message/create', 'Admin\Messenger\ThreadController@createMessage');
        });
    });

    Route::group(['prefix' => 'telegram'], function () {

        Route::get('getMe', 'Admin\Telegram\TelegramController@getMe');
        Route::get('getWebhookInfo', 'Admin\Telegram\TelegramController@getWebhookInfo');

        Route::group(['prefix' => 'message'], function () {
            Route::post('send', 'Admin\Telegram\MessageController@send');
        });

        Route::resource('webhook', 'Admin\Telegram\WebhookController');
    });

    Route::group(['prefix' => 'system'], static function () {
        //var_dump(ModelsController::class);die;
        Route::resource('models', 'Admin\System\ModelsController');
    });
});
