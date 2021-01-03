<?php

use App\Http\Controllers\AllergiesController;
use App\Http\Controllers\ConditionsController;
use App\Http\Controllers\CountriesController;
use App\Http\Controllers\HospitalDepartmentsController;
use App\Http\Controllers\HospitalsController;
use App\Http\Controllers\MedicationCategoriesController;
use App\Http\Controllers\MedicationsController;
use App\Http\Controllers\RegionsController;
use App\Http\Controllers\SymptomsController;
use App\Http\Controllers\ThirdPartyInsurancesController;
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

Route::get('/symptoms', [SymptomsController::class, 'index']);
Route::get('/symptoms/{id}', [SymptomsController::class, 'show']);
Route::post('/symptoms', [SymptomsController::class, 'store']);
Route::patch('/symptoms/{id}', [SymptomsController::class, 'update']);
Route::delete('/symptoms/{id}', [SymptomsController::class, 'destroy']);

Route::get('/medications/categories', [MedicationCategoriesController::class, 'index']);
Route::get('/medications/categories/full', [MedicationCategoriesController::class, 'full']);
Route::post('/medications/categories', [MedicationCategoriesController::class, 'store']);
Route::patch('/medications/categories/{id}', [MedicationCategoriesController::class, 'update']);
Route::delete('/medications/categories/{id}', [MedicationCategoriesController::class, 'destroy']);

Route::get('/medications', [MedicationsController::class, 'index']);
Route::get('/medications/{id}', [MedicationsController::class, 'show']);
Route::post('/medications', [MedicationsController::class, 'store']);
Route::patch('/medications/{id}', [MedicationsController::class, 'update']);
Route::delete('/medications/{id}', [MedicationsController::class, 'destroy']);

Route::post('/medications/{medication}/conditions/{condition}', [MedicationsController::class, 'storeCondition']);
Route::delete('/medications/{medication}/conditions/{condition}', [MedicationsController::class, 'destroyCondition']);

Route::post('/medications/{medication}/allergies/{allergy}', [MedicationsController::class, 'storeAllergy']);
Route::delete('/medications/{medication}/allergies/{allergy}', [MedicationsController::class, 'destroyAllergy']);

Route::post('/medications/{medication}/incompatibilities/{incompatibleWith}', [MedicationsController::class, 'storeIncompatibility']);
Route::delete('/medications/{medication}/incompatibilities/{incompatibleWith}', [MedicationsController::class, 'destroyIncompatibility']);
