<?php

use App\Http\Controllers\AllergiesController;
use App\Http\Controllers\ConditionsController;
use App\Http\Controllers\CountriesController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\HospitalDepartmentsController;
use App\Http\Controllers\HospitalsController;
use App\Http\Controllers\MedicationCategoriesController;
use App\Http\Controllers\MedicationsController;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\RegionsController;
use App\Http\Controllers\SymptomsController;
use App\Http\Controllers\ThirdPartyInsurancesController;
use App\Http\Controllers\UsersController;
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

Route::prefix('v1')->group(function() {
    Route::post('/login', [UsersController::class, 'login']);

    Route::middleware('auth:api')->group(function() {
        Route::get('/countries', [CountriesController::class, 'index']);
        Route::get('/countries/{isoCode}', [CountriesController::class, 'get']);
        Route::get('/countries/{isoCode}/regions', [RegionsController::class, 'countryIndex']);

        Route::get('/regions', [RegionsController::class, 'index']);
        Route::get('/regions/{id}', [RegionsController::class, 'get']);

        Route::get('/hospitals', [HospitalsController::class, 'index']);
        Route::post('/hospitals', [HospitalsController::class, 'store'])->middleware('superadmin');
        Route::patch('/hospitals/{id}', [HospitalsController::class, 'update'])->middleware('superadmin');
        Route::delete('/hospitals/{id}', [HospitalsController::class, 'destroy'])->middleware('superadmin');

        Route::get('/hospitals/{hospitalId}/departments', [HospitalDepartmentsController::class, 'index']);
        Route::post('/hospitals/{hospitalId}/departments', [HospitalDepartmentsController::class, 'store'])->middleware('hospital_admin');
        Route::patch('/hospitals/{hospitalId}/departments/{id}', [HospitalDepartmentsController::class, 'update'])->middleware('hospital_admin');
        Route::delete('/hospitals/{hospitalId}/departments/{id}', [HospitalDepartmentsController::class, 'destroy'])->middleware('hospital_admin');

        Route::get('/third-party-insurances', [ThirdPartyInsurancesController::class, 'index']);
        Route::post('/third-party-insurances', [ThirdPartyInsurancesController::class, 'store'])->middleware('superadmin');
        Route::patch('/third-party-insurances/{id}', [ThirdPartyInsurancesController::class, 'update'])->middleware('superadmin');
        Route::delete('/third-party-insurances/{id}', [ThirdPartyInsurancesController::class, 'destroy'])->middleware('superadmin');

        Route::get('/allergies', [AllergiesController::class, 'index']);
        Route::get('/allergies/{id}', [AllergiesController::class, 'show']);
        Route::post('/allergies', [AllergiesController::class, 'store'])->middleware('superadmin');
        Route::patch('/allergies/{id}', [AllergiesController::class, 'update'])->middleware('superadmin');
        Route::delete('/allergies/{id}', [AllergiesController::class, 'destroy'])->middleware('superadmin');

        Route::get('/conditions', [ConditionsController::class, 'index']);
        Route::get('/conditions/{id}', [ConditionsController::class, 'show']);
        Route::post('/conditions', [ConditionsController::class, 'store'])->middleware('superadmin');
        Route::patch('/conditions/{id}', [ConditionsController::class, 'update'])->middleware('superadmin');
        Route::delete('/conditions/{id}', [ConditionsController::class, 'destroy'])->middleware('superadmin');

        Route::get('/symptoms', [SymptomsController::class, 'index']);
        Route::get('/symptoms/{id}', [SymptomsController::class, 'show']);
        Route::post('/symptoms', [SymptomsController::class, 'store'])->middleware('superadmin');
        Route::patch('/symptoms/{id}', [SymptomsController::class, 'update'])->middleware('superadmin');
        Route::delete('/symptoms/{id}', [SymptomsController::class, 'destroy'])->middleware('superadmin');

        Route::get('/medications/categories', [MedicationCategoriesController::class, 'index']);
        Route::get('/medications/categories/full', [MedicationCategoriesController::class, 'full']);
        Route::post('/medications/categories', [MedicationCategoriesController::class, 'store'])->middleware('superadmin');
        Route::patch('/medications/categories/{id}', [MedicationCategoriesController::class, 'update'])->middleware('superadmin');
        Route::delete('/medications/categories/{id}', [MedicationCategoriesController::class, 'destroy'])->middleware('superadmin');

        Route::get('/medications', [MedicationsController::class, 'index']);
        Route::get('/medications/{id}', [MedicationsController::class, 'show']);
        Route::post('/medications', [MedicationsController::class, 'store'])->middleware('superadmin');
        Route::patch('/medications/{id}', [MedicationsController::class, 'update'])->middleware('superadmin');
        Route::delete('/medications/{id}', [MedicationsController::class, 'destroy'])->middleware('superadmin');
        Route::post('/medications/{medication}/conditions/{condition}', [MedicationsController::class, 'storeCondition'])->middleware('superadmin');
        Route::delete('/medications/{medication}/conditions/{condition}', [MedicationsController::class, 'destroyCondition'])->middleware('superadmin');
        Route::post('/medications/{medication}/allergies/{allergy}', [MedicationsController::class, 'storeAllergy'])->middleware('superadmin');
        Route::delete('/medications/{medication}/allergies/{allergy}', [MedicationsController::class, 'destroyAllergy'])->middleware('superadmin');
        Route::post('/medications/{medication}/incompatibilities/{incompatibleWith}', [MedicationsController::class, 'storeIncompatibility'])->middleware('superadmin');
        Route::delete('/medications/{medication}/incompatibilities/{incompatibleWith}', [MedicationsController::class, 'destroyIncompatibility'])->middleware('superadmin');

        Route::post('/users', [UsersController::class, 'store'])->middleware('hospital_admin');
        Route::patch('/users/{id}', [UsersController::class, 'update'])->middleware('hospital_admin');
        Route::delete('/users/{id}', [UsersController::class, 'destroy'])->middleware('hospital_admin');

        Route::get('/patients/{patient}/files', [FilesController::class, 'fromPatient']);
        Route::get('/visits/{visit}/files', [FilesController::class, 'fromVisit']);
        Route::get('/files/{id}', [FilesController::class, 'show']);
        Route::post('/files', [FilesController::class, 'store']);
        Route::delete('/files/{id}', [FilesController::class, 'destroy'])->middleware('hospital_admin');

        Route::get('/patients', [PatientsController::class, 'index']);
        Route::get('/patients/{id}', [PatientsController::class, 'show']);
        Route::get('/patients/{isoCode}/{id}', [PatientsController::class, 'showByCountryId']);
        Route::patch('/patients/{id}', [PatientsController::class, 'update']);
        Route::delete('/patients/{id}', [PatientsController::class, 'destroy'])->middleware('superadmin');
    });
});
