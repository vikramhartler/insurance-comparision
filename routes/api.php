<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('insurance')->group(function () {
    Route::get('/years', 'InsuranceApiController@getYears');
    Route::get('/makes', 'InsuranceApiController@getMakes');
    Route::post('/models', 'InsuranceApiController@getModels');
    Route::get('/auto-insurance', 'InsuranceApiController@autoInsurance');
    Route::post('/search', 'InsuranceApiController@searchQuotes');
});