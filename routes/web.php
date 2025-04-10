<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', [AuthController::class,  'getLogin']);
Route::get('/information-sheet', function () {
    return view('information_sheet');
});


// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// authenticated routes
Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
});