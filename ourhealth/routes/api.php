<?php

use App\Http\Controllers\AllergiesController;
use App\Http\Controllers\ConditionsController;
use App\Http\Controllers\CountriesController;
use App\Http\Controllers\HospitalDepartmentsController;
use App\Http\Controllers\HospitalsController;
use App\Http\Controllers\RegionsController;
use App\Http\Controllers\ThirdPartyInsurancesController;
use App\Models\HospitalDepartment;
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

Route::get('/hospitals/{hospitalId}/departments', [HospitalDepartmentsController::class, 'index']);
Route::post('/hospitals/{hospitalId}/departments', [HospitalDepartmentsController::class, 'store']);
Route::patch('/hospitals/{hospitalId}/departments/{id}', [HospitalDepartmentsController::class, 'update']);
Route::delete('/hospitals/{hospitalId}/departments/{id}', [HospitalDepartmentsController::class, 'destroy']);

Route::get('/third-party-insurances', [ThirdPartyInsurancesController::class, 'index']);
Route::post('/third-party-insurances', [ThirdPartyInsurancesController::class, 'store']);
Route::patch('/third-party-insurances/{id}', [ThirdPartyInsurancesController::class, 'update']);
Route::delete('/third-party-insurances/{id}', [ThirdPartyInsurancesController::class, 'destroy']);

Route::get('/allergies', [AllergiesController::class, 'index']);
Route::get('/allergies/{id}', [AllergiesController::class, 'show']);
Route::post('/allergies', [AllergiesController::class, 'store']);
Route::patch('/allergies/{id}', [AllergiesController::class, 'update']);
Route::delete('/allergies/{id}', [AllergiesController::class, 'destroy']);

Route::get('/conditions', [ConditionsController::class, 'index']);
Route::get('/conditions/{id}', [ConditionsController::class, 'show']);
Route::post('/conditions', [ConditionsController::class, 'store']);
Route::patch('/conditions/{id}', [ConditionsController::class, 'update']);
Route::delete('/conditions/{id}', [ConditionsController::class, 'destroy']);
