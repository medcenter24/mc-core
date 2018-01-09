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

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'role:admin']], function () {
    Route::get('/', 'Admin\MainController@index');
    Route::resource('users', 'Admin\UsersController');
    Route::resource('roles', 'Admin\RolesController');
    Route::group(['prefix' => 'preview'], function() {
        Route::get('caseReport', 'Admin\PreviewController@caseReport');
        Route::get('caseHistory', 'Admin\PreviewController@caseHistory');
    });
    Route::get('cases', 'Admin\CasesController@search');
    Route::get('cases/report', 'Admin\CasesController@report');
    Route::get('cases/pdf', 'Admin\CasesController@downloadPdf');
    Route::get('cases/history', 'Admin\CasesController@history');
});
