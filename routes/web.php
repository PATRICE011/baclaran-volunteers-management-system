<?php

use App\Http\Controllers\ArchivesController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\MinistryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchedulesController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SignController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\VolunteersController;
use App\Http\Controllers\AccountSettingsController;
use Illuminate\Support\Facades\Auth;

Route::get('/', [AuthController::class,  'getLogin']);

Route::post('/login', [AuthController::class, 'login'])->name('authorizeUser');
Route::get('/sign-in', [SignController::class, 'index']);
Route::get('/forgot-password', [ForgotPasswordController::class, 'index']);
// authenticated routes
Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/schedule', [SchedulesController::class, 'index']);
    Route::get('/ministries', [MinistryController::class, 'index']);
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::get('/tasks', [TasksController::class, 'index']);
    
    Route::get('/role', [RoleController::class, 'index']);
    Route::get('/archives', [ArchivesController::class, 'index']);
   

    // volunteers page
    Route::prefix('volunteers')->group(function () {
        Route::get('/', [VolunteersController::class, 'index']);
        Route::post('/register', [VolunteersController::class, 'store'])->name('volunteers.register');
        Route::get('/{id}', [VolunteersController::class, 'show'])->name('volunteers.show');
        Route::get('/{id}/edit', [VolunteersController::class, 'edit'])->name('volunteers.edit');
        Route::put('/{id}', [VolunteersController::class, 'update'])->name('volunteers.update');
        // Route::delete('/{id}', [VolunteersController::class, 'destroy'])->name('volunteers.destroy');
    });

    // account settings page
    Route::prefix('settings')->group(function(){
        Route::get('/', [SettingsController::class, 'index']);
        Route::prefix('account')->name('account.')->group(function () {
       
        Route::get('/user-data', [AccountSettingsController::class, 'getUserData'])->name('user.data');
        
      
        Route::post('/name-change/request-otp', [AccountSettingsController::class, 'requestNameChangeOTP'])->name('name.change.request');
        Route::post('/name-change/verify-otp', [AccountSettingsController::class, 'verifyNameChangeOTP'])->name('name.change.verify');
     
        Route::post('/resend-otp', [AccountSettingsController::class, 'resendOTP'])->name('resend.otp');
        
        });
    });
    
});
