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
});

Route::group(['prefix' => 'doctor', 'middleware' => ['auth', 'role:doctor']], function () {
    Route::get('/', 'Doctor\MainController@index');
});

Route::group(['prefix' => 'director', 'middleware' => ['auth', 'role:director']], function () {
    Route::get('/', 'Director\MainController@index');
    Route::resource('/accidentStatus', 'Director\AccidentStatusController');
});
