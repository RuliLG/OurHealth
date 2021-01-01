<?php

use App\Http\Controllers\CountriesController;
use App\Http\Controllers\HospitalsController;
use App\Http\Controllers\RegionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/countries', [CountriesController::class, 'index']);
Route::get('/countries/{isoCode}', [CountriesController::class, 'get']);
Route::get('/countries/{isoCode}/regions', [RegionsController::class, 'countryIndex']);

Route::get('/regions', [RegionsController::class, 'index']);
Route::get('/regions/{id}', [RegionsController::class, 'get']);

Route::get('/hospitals', [HospitalsController::class, 'index']);
Route::post('/hospitals', [HospitalsController::class, 'store']);
Route::patch('/hospitals/{id}', [HospitalsController::class, 'update']);
Route::delete('/hospitals/{id}', [HospitalsController::class, 'destroy']);
