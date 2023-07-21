<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubCategoryController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function(){
    Route::get('/students/current-ojts', [StudentController::class, 'currentOJTs']);
    Route::apiResource('students', StudentController::class);
    Route::get('/attendances', [AttendanceController::class, 'index']);

    Route::get('/student/{student}/attendances', [AttendanceController::class, 'student_attendances']);
    Route::post('student/{student}/attendances', [AttendanceController::class, 'enter']);
    Route::post('student/{student}/attendances/manual', [AttendanceController::class, 'manual']);
    Route::post('student/{student}/attendances/manual-remove', [AttendanceController::class, 'manual_remove']);
    Route::post('student/{student}/attendances/absent', [AttendanceController::class, 'absent']);;
    Route::put('student/{student}/attendances/{attendance}/leave', [AttendanceController::class, 'leave']);
    Route::delete('student/{student}/attendances/{attendance}', [AttendanceController::class, 'destroy']);
    Route::put('student/{student}/attendances/{attendance}/re-enter', [AttendanceController::class, 'relogin']);

    Route::apiResource('categories', CategoryController::class);
    Route::put('sub_categories/{sub_category}', [SubCategoryController::class, 'update']);
    Route::get('sub_categories', [SubCategoryController::class, 'index']);
    Route::apiResource('category.sub_categories', SubCategoryController::class)->only(['store', 'destroy']);
    Route::apiResource('sub_category.courses', CourseController::class);
    Route::get('/student/{student}/courses', [CourseController::class, 'index']);
    Route::post('/course/{course}/add_attendee', [CourseController::class, 'add_attendee']);
    Route::delete('/course/{course}/student/{student}/remove_attendee', [CourseController::class, 'remove_attendee']);
    Route::post('/students/{student}/change-profile', [ImageController::class, 'changeProfile']);
    Route::post('/sub_category/{sub_category}/courses/{course}/change-image', [ImageController::class, 'changeCourseImage']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::put('/change-password', [AuthController::class, 'change_password']);

});


Route::post('/login', [AuthController::class, 'login']);
Route::post('/validation/check-credentials', [AuthController::class, 'check_credentials']);
