<?php

use App\Http\Controllers\ArchivesController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\MinistryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\VolunteersController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\EventController;

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
    // 
    // Task Management Routes
    Route::prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'index']);
    });

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TasksController::class, 'index'])->name('tasks.index');
        Route::post('/', [TasksController::class, 'store'])->name('tasks.store');
        Route::get('/{task}/edit', [TasksController::class, 'edit'])->name('tasks.edit');
        Route::put('/{task}', [TasksController::class, 'update'])->name('tasks.update');
        Route::delete('/{task}', [TasksController::class, 'destroy'])->name('tasks.destroy');

        Route::post('/{task}/archive', [TasksController::class, 'archive'])->name('tasks.archive');
        Route::post('/{task}/restore', [TasksController::class, 'restore'])->name('tasks.restore');
        Route::delete('/{task}/force-delete', [TasksController::class, 'forceDelete'])->name('tasks.forceDelete');
        Route::post('/bulk-restore', [TasksController::class, 'bulkRestore'])->name('tasks.bulkRestore');
        Route::post('/bulk-force-delete', [TasksController::class, 'bulkForceDelete'])->name('tasks.bulkForceDelete');
    });

    // Event routes - Modified to match our new implementation
    Route::prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('events.index');
        Route::post('/', [EventController::class, 'store'])->name('events.store');

        // Volunteer search route (outside the {event} group since it's not specific to one event)
        Route::get('/volunteers/search', [EventController::class, 'searchVolunteers'])->name('events.volunteers.search');

        Route::post('/{event}/restore', [EventController::class, 'restore'])->name('events.restore');
        Route::delete('/{event}/force-delete', [EventController::class, 'forceDelete'])->name('events.forceDelete');

        Route::post('/bulk-restore', [EventController::class, 'bulkRestore'])->name('events.bulkRestore');
        Route::post('/bulk-force-delete', [EventController::class, 'bulkForceDelete'])->name('events.bulkForceDelete');
        // Single event operations
        Route::prefix('{event}')->group(function () {
            Route::get('/', [EventController::class, 'show'])->name('events.show');
            Route::put('/', [EventController::class, 'update'])->name('events.update');
            Route::post('/archive', [EventController::class, 'archive'])->name('events.archive');

            // Volunteer-related routes
            Route::get('/volunteers', [EventController::class, 'getEventVolunteers'])->name('events.volunteers');

            // New volunteer management routes
            Route::post('/volunteers/{volunteer}', [EventController::class, 'addVolunteer'])->name('events.volunteers.add');
            Route::delete('/volunteers/{volunteer}', [EventController::class, 'removeVolunteer'])->name('events.volunteers.remove');

            // Attendance routes
            Route::prefix('attendance')->group(function () {
                Route::post('/save', [EventController::class, 'saveAttendance'])->name('events.attendance.save');
            });
        });
    });

    // volunteers page
    Route::prefix('volunteers')->group(function () {
        Route::get('/', [VolunteersController::class, 'index']);
        Route::post('/register', [VolunteersController::class, 'store'])->name('volunteers.register');
        Route::get('/{id}', [VolunteersController::class, 'show'])->name('volunteers.show');
        Route::get('/{id}/edit', [VolunteersController::class, 'edit'])->name('volunteers.edit');
        Route::put('/{id}', [VolunteersController::class, 'update'])->name('volunteers.update');
        // Route::delete('/{id}', [VolunteersController::class, 'destroy'])->name('volunteers.destroy');
        Route::post('/{volunteer}/timeline', [VolunteersController::class, 'updateTimeline'])->name('volunteers.timeline.update');
        Route::post('/{volunteer}/affiliation', [VolunteersController::class, 'updateAffiliation'])->name('volunteers.affiliation.update');
        Route::put('/{id}/sacraments', [VolunteersController::class, 'updateSacraments']);
        Route::put('/{id}/formations', [VolunteersController::class, 'updateFormations']);
        Route::put('/{id}/complete-update', [VolunteersController::class, 'completeUpdate'])->name('volunteers.complete-update');

        Route::post('/{volunteer}/picture', [VolunteersController::class, 'updateProfilePicture'])->name('volunteers.picture.update');

        Route::post('/{volunteer}/archive', [VolunteersController::class, 'archive'])->name('volunteers.archive');
        Route::post('/{volunteer}/restore', [VolunteersController::class, 'restore']);
        Route::delete('/{volunteer}/force-delete', [VolunteersController::class, 'forceDelete'])->name('volunteers.forceDelete');
        Route::post('/bulk-restore', [VolunteersController::class, 'bulkRestore'])->name('volunteers.bulkRestore');
        Route::post('/bulk-force-delete', [VolunteersController::class, 'bulkForceDelete'])->name('volunteers.bulkForceDelete');

        Route::get('/export/excel', [VolunteersController::class, 'exportExcel'])->name('volunteers.export');
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
        Route::prefix('role')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::post('', [RoleController::class, 'store'])->name('store');
            Route::put('{user}', [RoleController::class, 'update'])->name('update');

            Route::post('/{user}/archive', [RoleController::class, 'archive'])->name('archive');
            Route::post('/{user}/restore', [RoleController::class, 'restore'])->name('restore');
            Route::delete('/{user}/force-delete', [RoleController::class, 'forceDelete'])->name('forceDelete');

            Route::post('/bulk-restore', [RoleController::class, 'bulkRestore'])->name('bulkRestore');
            Route::post('/bulk-force-delete', [RoleController::class, 'bulkForceDelete'])->name('bulkForceDelete');
        });

        Route::prefix('archives')->group(function () {
            Route::get('/', [ArchivesController::class, 'index']);
        });
    });
});
