<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

// ======================
// PUBLIC PAGES
// ======================
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// ======================
// REGISTER (Multi-step with 2FA)
// ======================
Route::get('/register', function () {
    return view('register');
})->name('register');

Route::post('/register', [UserController::class, 'userRegister'])
     ->name('register.submit');

// ======================
// LOGIN (Multi-step with 2FA)
// ======================
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [UserController::class, 'loginUser'])
     ->name('login.submit');

// ======================
// 2FA ROUTES
// ======================
Route::post('/2fa/verify', [UserController::class, 'verify2FA'])->name('2fa.verify');
Route::post('/2fa/enable', [UserController::class, 'enable2FA'])->name('2fa.enable');

// ======================
// PROTECTED ROUTES (Sanctum)
// ======================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/me', [UserController::class, 'me'])->name('user.me');
});

// ======================
// FORGOT PASSWORD (Optional)
// ======================
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

Route::post('/2fa/toggle', [UserController::class, 'toggle2FA']);

// Route::get('/me', [UserController::class, 'me'])->name('user.me');

// Route::post('/user/2fa/toggle', [UserController::class, 'toggle2FA'])->name('user.2fa.toggle');

// Add this
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/me', [UserController::class, 'me'])->name('user.me');
    
    Route::post('/2fa/toggle', [UserController::class, 'toggle2FA']);

});

Route::post('/reset-password', [UserController::class, 'resetPassword'])
     ->name('password.update');