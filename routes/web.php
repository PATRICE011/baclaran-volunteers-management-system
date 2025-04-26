<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SchedulesController;
use App\Http\Controllers\VolunteersController;
use Illuminate\Support\Facades\Auth;

Route::get('/', [AuthController::class,  'getLogin']);

Route::post('/login', [AuthController::class, 'login'])->name('authorizeUser');

// authenticated routes
Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/volunteers', [VolunteersController::class, 'index']);
    Route::get('/schedule', [SchedulesController::class, 'index']);
    
});
