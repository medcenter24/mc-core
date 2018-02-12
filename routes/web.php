<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
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
});
