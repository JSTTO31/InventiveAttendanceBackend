<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubCategoryController;
use App\Models\Attendance;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function(){
    Route::get('/students/current-ojts', [StudentController::class, 'currentOJTs']);
    Route::apiResource('students', StudentController::class);
    Route::get('/attendances', [AttendanceController::class, 'index']);
    Route::get('/student/{student}/attendances', [AttendanceController::class, 'student_attendances']);
    Route::post('student/{student}/attendances', [AttendanceController::class, 'enter']);
    Route::post('student/{student}/attendances/manual', [AttendanceController::class, 'manual']);
    Route::post('student/{student}/attendances/absent', [AttendanceController::class, 'absent']);;
    Route::put('student/{student}/attendances/{attendance}/leave', [AttendanceController::class, 'leave']);
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

});


Route::post('/login', [AuthController::class, 'login']);
Route::post('/validation/check-credentials', [AuthController::class, 'check_credentials']);


Route::get('/test', function(){

    $attendances = Attendance::where('work_time', '>', 0)->get();
    $attendances->each(function($attendance){
        $time_in_hour = (float) Carbon::parse($attendance->time_in,)->setTimezone('Asia/Manila')->format('H');
        $time_in_minute = (float) Carbon::parse($attendance->time_in,)->setTimezone('Asia/Manila')->format('i');
        $time_out_hour = (float) Carbon::parse($attendance->time_out,)->setTimezone('Asia/Manila')->format('H');
        $time_out_minute = (float) Carbon::parse($attendance->time_out,)->setTimezone('Asia/Manila')->format('i');
        dump($time_in_hour, $time_in_minute);
    });
    return $attendances;


    // try {
    //     $sub_categories = SubCategory::all();

    //     $conn = new mysqli('localhost', 'root', null, 'attendance_db');

    //     if($conn->connect_error){
    //         die('Failed connection: ' .  $conn->connect_error);
    //     }


    //     $stmt = $conn->prepare('INSERT INTO sub_categories (id, name, category_id) VALUES (?,?,?)');
    //     $id = null;
    //     $name = null;
    //     $category = null;
    //     $stmt->bind_param('sss', $id, $name, $category);

    //     foreach($sub_categories as $sub_category){
    //         $id = $sub_category->id;
    //         $name = $sub_category->name;
    //         $category = $sub_category->category_id;
    //         $stmt->execute();
    //     }
    // } catch (\Throwable $th) {
    //     echo $th;
    // }




    // SubCategory::all()->each(function(SubCategory $sub_category) use($stmt){
    //     $name = $sub_category->name;
    //     $category = $sub_category->category;

    //     $stmt->execute();
    // });



    // return $conn;
});

