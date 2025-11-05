<?php

declare(strict_types=1);

use Lightit\Users\App\Controllers\GetCurrentUserController;
use Illuminate\Support\Facades\Route;
use Lightit\Authentication\App\Controllers\{LoginController, LogoutController, RefreshController};
use Lightit\Users\App\Controllers\{GetUserController, DeleteUserController, ListUserController, StoreUserController, UpdateUserController};
use Lightit\Doctors\App\Controllers\{GetDoctorController, ListDoctorController, StoreDoctorController, UpdateDoctorController, DeleteDoctorController, AssignClinicsToDoctorController};
use Lightit\Clinics\App\Controllers\{GetClinicController, ListClinicController, StoreClinicController, UpdateClinicController, DeleteClinicController, AssignDoctorsToClinicController};
use Lightit\Patients\App\Controllers\{GetPatientController, ListPatientController, StorePatientController, UpdatePatientController, DeletePatientController};
use Lightit\Appointments\App\Controllers\{StoreAppointmentController, DeleteAppointmentController};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')
    ->get('/me', GetCurrentUserController::class);

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(static function (): void {
    Route::post('login', LoginController::class);
    Route::post('logout', LogoutController::class);
    Route::post('refresh', RefreshController::class);
});

/*
|--------------------------------------------------------------------------
| Users Routes
|--------------------------------------------------------------------------
*/
Route::prefix('users')
    ->middleware([])
    ->group(static function (): void {
        Route::get('/', ListUserController::class);
        Route::get('/{user}', GetUserController::class)
            ->withTrashed()
            ->whereNumber('user');
        Route::post('/', StoreUserController::class);
        Route::put('/{user}', UpdateUserController::class)
            ->whereNumber('user');
        Route::delete('/{user}', DeleteUserController::class)
            ->whereNumber('user');
    });

/*
|--------------------------------------------------------------------------
| Doctors Routes
|--------------------------------------------------------------------------
*/
Route::prefix('doctors')->group(static function (): void {
    Route::prefix('{doctor}')->group(static function (): void {
        Route::get('/', GetDoctorController::class)->withTrashed();
        Route::put('/', UpdateDoctorController::class);
        Route::delete('/', DeleteDoctorController::class);

        Route::put('/clinics', AssignClinicsToDoctorController::class);
    })->whereNumber('doctor');

    Route::get('/', ListDoctorController::class);
    Route::post('/', StoreDoctorController::class);
});

/*
|--------------------------------------------------------------------------
| Clinics Routes
|--------------------------------------------------------------------------
*/
Route::prefix('clinics')->group(static function (): void {
    Route::prefix('{clinic}')->group(static function (): void {
        Route::get('/', GetClinicController::class)->withTrashed();
        Route::put('/', UpdateClinicController::class);
        Route::delete('/', DeleteClinicController::class);

        Route::put('/doctors', AssignDoctorsToClinicController::class);
    })->whereNumber('clinic');

    Route::get('/', ListClinicController::class);
    Route::post('/', StoreClinicController::class);
});

/*
|--------------------------------------------------------------------------
| Patients Routes
|--------------------------------------------------------------------------
*/
Route::prefix('patients')->group(static function (): void {
    Route::prefix('{patient}')->group(static function (): void {
        Route::get('/', GetPatientController::class)->withTrashed();
        Route::put('/', UpdatePatientController::class);
        Route::delete('/', DeletePatientController::class);
    })->whereNumber('patient');

    Route::get('/', ListPatientController::class);
    Route::post('/', StorePatientController::class);
});

/*
|--------------------------------------------------------------------------
| Appointments Routes
|--------------------------------------------------------------------------
*/
Route::prefix('appointments')->group(static function (): void {
    Route::post('/', StoreAppointmentController::class);

    Route::delete('/{appointment}', DeleteAppointmentController::class)
            ->whereNumber('appointment');
});
