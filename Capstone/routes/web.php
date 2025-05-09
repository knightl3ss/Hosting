<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\NosaController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\JobListController;
use App\Http\Controllers\ServiceRecordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\AuthController\LoginController;
use App\Http\Controllers\AuthController\RegisterController;
use App\Http\Controllers\AppointmentTypeController;
use App\Http\Controllers\NotificationController;

// Public front page route (shows login modal for guests, dashboard/appointment for logged-in)
Route::get('/', function () {
    return view('Pages.front_page');
});

// Appointment Types Display Page (from controller)
Route::get('/appointment-types', [AppointmentTypeController::class, 'index'])->name('appointment.types');

// Auth routes
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::post('/register', [RegisterController::class, 'register'])->name('register');

// Protected routes (require authentication and prevent back history)
Route::middleware(['auth', 'prevent-back'])->group(function () {
    // Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Include Service Record routes
    require base_path('routes/servicerecord.php');
    // Include Appointment routes
    require base_path('routes/appointment.php');
    // Include Plantilla routes
    // require base_path('routes/plantilla.php');
});

// Notification routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});

Route::get('/settings', function () {
    return view('Pages.Additional.setting');
});

Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile.show');
Route::put('/profile/update', [ProfileController::class, 'updateAccount'])->name('profile.update');

// Password reset OTP routes
Route::post('/password/send-otp', [\App\Http\Controllers\PasswordResetController::class, 'sendOTP'])->name('password.sendOtp');
Route::post('/password/verify-otp', [\App\Http\Controllers\PasswordResetController::class, 'verifyOTP'])->name('password.verifyOtp');
Route::post('/password/reset', [\App\Http\Controllers\PasswordResetController::class, 'resetPassword'])->name('password.reset');

Route::post('/register-modal', [RegisterController::class, 'registerModal'])->name('register.modal');

Route::delete('/admin/delete/{id}', [RegisterController::class, 'deleteAdmin'])->name('admin.delete');

Route::get('/test-otp', function () {
    return view('test_otp_route');
});

// Add check-unique route for AJAX uniqueness validation
Route::post('/check-unique', [RegisterController::class, 'checkUnique'])->name('check.unique');

// Add route for manually setting appointment as active
Route::post('/appointments/{id}/set-active', [\App\Http\Controllers\AppointmentController\AppointmentScheduleController::class, 'setActive'])->name('appointments.setActive');

// AJAX endpoint for real-time uniqueness check
Route::post('/appointment/check-unique', [App\Http\Controllers\AppointmentController\AppointmentController::class, 'checkUnique'])->name('appointment.checkUnique');
