<?php

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

Route::get('/create-csv', 'InsuranceController@create')->name('create-csv');
Route::get('/set-insurance', 'InsuranceController@setCollection')->name('set-data');
Route::get('/get-insurance', 'InsuranceController@getData')->name('get-data');