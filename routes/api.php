<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VacancyApplyController;
use App\Http\Controllers\Api\VacancyController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/roles', [AuthController::class, 'roles']);
});

// vacancy list
Route::get('/vacancies', [VacancyController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {

    // Company
    Route::middleware('role:company')->group(function () {
        Route::post('/vacancies', [VacancyController::class, 'store']);
        Route::put('/vacancies/{id}', [VacancyController::class, 'update']);
        Route::get('/vacancies/my', [VacancyController::class, 'myJobs']);
    });

    // Freelancer
    Route::middleware('role:freelancer')->group(function () {
        Route::post('/vacancies/{id}/apply', [VacancyApplyController::class, 'apply']);
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
});
