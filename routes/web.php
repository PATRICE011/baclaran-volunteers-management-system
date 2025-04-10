<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;

Route::get('/', [AuthController::class,  'getLogin']);
Route::get('/information-sheet', function () {
    return view('information_sheet');
});


Route::post('/login', [AuthController::class, 'login'])->name('authorizeUser');



// authenticated routes
Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});