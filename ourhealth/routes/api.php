<?php

use App\Http\Controllers\CountriesController;
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

Route::get('/regions/{id}', [RegionsController::class, 'get']);
