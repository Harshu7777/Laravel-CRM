<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'userRegister']);
Route::post('/login', [UserController::class, 'loginUser']);
Route::post('/refresh-token', [UserController::class, 'refreshToken']);

Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);

    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::get('/{id}', [StudentController::class, 'show']);
        Route::post('/', [StudentController::class, 'store']);
        Route::put('/{id}', [StudentController::class, 'update']);
        Route::delete('/{id}', [StudentController::class, 'destroy']);
        Route::get('/state/{state}', [StudentController::class, 'filterByState']);
    });

});

