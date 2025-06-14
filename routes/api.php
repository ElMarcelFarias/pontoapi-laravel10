<?php

use App\Http\Controllers\AttendanceRecordController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\ClockDailyController;
use App\Http\Controllers\JWTAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [JWTAuthController::class, 'login']);

Route::group(['middleware' => ['jwt']], function () {
    Route::post('/clockin', [ClockDailyController::class, 'registerClockIn']);

    Route::get('/today-records', [AttendanceRecordController::class, 'getTodayRecords']);
});

Route::group(['middleware' => ['jwt', 'admin']], function () {
    Route::post('/logout', [JWTAuthController::class, 'logout']);
    Route::post('/register', [JWTAuthController::class, 'register']);
    Route::post('/attendance-report', [AttendanceRecordController::class, 'generateAttendanceReport']);

    Route::put('/user/{id}', [JWTAuthController::class, 'update']);

    Route::get('/user/{id}', [JWTAuthController::class, 'getUser']);
    Route::get('/user', [JWTAuthController::class, 'getAllUsers']);
    Route::get('/download-report/{fileName}', [AttendanceReportController::class, 'downloadReport'])->name('download.report');

    Route::delete('/user/{id}', [JWTAuthController::class, 'destroy']);
});

