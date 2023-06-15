<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\StudentController;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/students/current-ojts', [StudentController::class, 'currentOJTs']);
Route::post('/students/{student}/change-profile', [ImageController::class, 'changeProfile']);
Route::apiResource('students', StudentController::class);
Route::get('/attendances', [AttendanceController::class, 'index']);
Route::get('/student/{student}/attendances', [AttendanceController::class, 'student_attendances']);
Route::post('student/{student}/attendances', [AttendanceController::class, 'enter']);
Route::post('student/{student}/attendances/absent', [AttendanceController::class, 'absent']);;
Route::put('student/{student}/attendances/{attendance}/leave', [AttendanceController::class, 'leave']);



