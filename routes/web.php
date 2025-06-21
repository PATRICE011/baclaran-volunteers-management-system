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
use App\Http\Controllers\TasksController;
use App\Http\Controllers\VolunteersController;
use App\Http\Controllers\AccountSettingsController;


Route::get('/', [AuthController::class, 'getLogin'])
    ->name('login')
    ->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('authorizeUser');

// ======== RESET PASSWORD ============
Route::get('/find-email', [ForgotPasswordController::class, 'showFindEmailForm'])->name('password.request');
Route::post('/find-email', [ForgotPasswordController::class, 'sendResetOtp'])->name('password.email');
Route::get('/request-otp', [ForgotPasswordController::class, 'showOtpForm'])->name('password.otp');
Route::post('/request-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verify');
Route::get('/make-new-password', [ForgotPasswordController::class, 'showNewPasswordForm'])->name('password.reset');
Route::post('/make-new-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
Route::post('/resend-otp', [ForgotPasswordController::class, 'resendOtp'])->name('password.otp.resend');

Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/schedule', [SchedulesController::class, 'index']);


    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::get('/tasks', [TasksController::class, 'index']);


    // volunteers page
    Route::prefix('volunteers')->group(function () {
        Route::get('/', [VolunteersController::class, 'index']);
        Route::post('/register', [VolunteersController::class, 'store'])->name('volunteers.register');
        Route::get('/{id}', [VolunteersController::class, 'show'])->name('volunteers.show');
        Route::get('/{id}/edit', [VolunteersController::class, 'edit'])->name('volunteers.edit');
        Route::put('/{id}', [VolunteersController::class, 'update'])->name('volunteers.update');
        // Route::delete('/{id}', [VolunteersController::class, 'destroy'])->name('volunteers.destroy');
        Route::post('/{volunteer}/archive', [VolunteersController::class, 'archive'])->name('volunteers.archive');
        Route::post('/{volunteer}/restore', [VolunteersController::class, 'restore'])->name('volunteers.restore');
        Route::delete('/{volunteer}/force-delete', [VolunteersController::class, 'forceDelete'])->name('volunteers.forceDelete');
    });

    // ministry page
    Route::prefix('ministries')->group(function () {
        Route::get('/', [MinistryController::class, 'index'])->name('ministries.index');
        Route::post('/', [MinistryController::class, 'store'])->name('ministries.store');
        Route::get('/{id}', [MinistryController::class, 'show'])->name('ministries.show');
        Route::put('/{id}', [MinistryController::class, 'update'])->name('ministries.update');
        Route::delete('/{id}', [MinistryController::class, 'destroy'])->name('ministries.destroy');
        Route::get('/parents/list', [MinistryController::class, 'getParentMinistries'])->name('ministries.parents');
        // Route::get('/parents', [MinistryController::class, 'getParentMinistries'])->name('ministries.parents');
        Route::get('/stats', [MinistryController::class, 'getStats'])->name('ministries.stats');
    });

    //settings page
    Route::prefix('settings')->group(function () {
        Route::get('/', [AccountSettingsController::class, 'index']);

        Route::prefix('account')->name('account.')->group(function () {

            Route::get('/user-data', [AccountSettingsController::class, 'getUserData'])->name('user.data');
            Route::post('/name-change/request-otp', [AccountSettingsController::class, 'requestNameChangeOTP']);
            Route::post('/name-change/verify-otp', [AccountSettingsController::class, 'verifyOTP']);

            Route::post('/email-change/request-otp', [AccountSettingsController::class, 'requestEmailChangeOTP']);
            Route::post('/email-change/verify-otp', [AccountSettingsController::class, 'verifyEmailOTP']);

            Route::post('/password-change/request-otp', [AccountSettingsController::class, 'requestPasswordChangeOTP']);
            Route::post('/password-change/verify-otp', [AccountSettingsController::class, 'verifyPasswordChangeOTP']);

            Route::post('/resend-otp', [AccountSettingsController::class, 'resendOTP'])->name('resend.otp');


            Route::post('update-profile-picture', [AccountSettingsController::class, 'updateProfilePicture']);
        });


        // role management
        Route::prefix('role')->group(function () {
            Route::get('/', [RoleController::class, 'index']);
            Route::post('', [RoleController::class, 'store'])->name('roles.store');
            Route::put('{user}', [RoleController::class, 'update'])->name('roles.update');
            // Route::delete('/{user}', [RoleController::class, 'destroy'])->name('roles.destroy'); 
        });

        Route::prefix('archives')->group(function () {
            Route::get('/', [ArchivesController::class, 'index']);
        });
    });
});
