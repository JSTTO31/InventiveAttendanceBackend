<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(){
        return Attendance::all();
    }

    public function enter(Request $request, Student $student){
        $exists = Attendance::where('student_id', $student->id)->whereDate('created_at', '>=', Carbon::today('Asia/Manila'))->exists();

        if($exists){
            abort(403, 'Student already enter!');
        }

        $attendance = $student->attendances()->create([
            'time_in' => Carbon::now('Asia/Manila')->subHour(),
        ]);

        return $attendance;
    }

    public function leave(Request $request, Student $student, Attendance $attendance){
        $now = now();
        $work_time = Carbon::parse($now)->floatDiffInHours(Carbon::parse($attendance->time_in));
        $attendance->time_out = $now;
        $attendance->work_time = $work_time;
        $attendance->save();

        return $attendance;
    }

    public function absent(Request $request, Student $student){
        $exists = Attendance::where('student_id', $student->id)->whereDate('created_at', '>=', Carbon::today())->exists();

        if($exists){
            abort(403, 'Student already have record!');
        }

        $attendance = $student->attendances()->create([
            'is_absent' => true,
        ]);

        return $attendance;
    }
}
