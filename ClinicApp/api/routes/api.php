<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/users/{user}', [UserController::class, 'show']);
Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'userProfile']);

    // Common routes for patients and doctors
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancelAppointment']);
    Route::patch('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirmAppointment']);

    // Notification
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Update profile
    Route::put('/profile', [AuthController::class, 'updateProfile']);
});

// Routes for doctors
Route::middleware(['auth:sanctum', 'doctor'])->group(function () {
    Route::apiResource('schedules', ScheduleController::class);
});

// Routes for patients
Route::middleware(['auth:sanctum', 'patient'])->group(function () {
    Route::get('/doctors', [AppointmentController::class, 'findDoctors']);
    Route::get('/doctors/{doctor}/availability', [AppointmentController::class, 'checkAvailability']);
    Route::post('/appointments/book', [AppointmentController::class, 'bookAppointment']);
    Route::get('/appointments', [AppointmentController::class, 'patientAppointments']);
});

// Routes for doctors (agenda)
Route::middleware(['auth:sanctum', 'doctor'])->group(function () {
    Route::get('/doctor/appointments', [AppointmentController::class, 'doctorAppointments']);
});

// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/stats', [AdminController::class, 'dashboardStats']);
    Route::get('/admin/stats/specialties', [AdminController::class, 'specialtyStats']);
    Route::get('/admin/stats/doctors', [AdminController::class, 'doctorStats']);
    Route::get('/admin/doctors', [AdminController::class, 'getDoctors']);
    Route::post('/admin/doctors', [AdminController::class, 'createDoctor']);
    Route::put('/admin/doctors/{doctor}', [AdminController::class, 'updateDoctor']);
    Route::delete('/admin/doctors/{doctor}', [AdminController::class, 'deleteDoctor']);
});
