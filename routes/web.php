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
    
    Route::get('/schedule', [SchedulesController::class, 'index']);
    
    // volunteers page
    Route::prefix('volunteers')->group(function(){
        Route::get('/', [VolunteersController::class, 'index']);
        Route::post('/register', [VolunteersController::class, 'store'])->name('volunteers.register');
        Route::get('/{id}', [VolunteersController::class, 'show'])->name('volunteers.show');
        Route::delete('/{id}', [VolunteersController::class, 'destroy'])->name('volunteers.destroy');
    });
    

});
