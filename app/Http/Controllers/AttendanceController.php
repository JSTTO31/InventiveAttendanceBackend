<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(){
        return Attendance::all();
    }

    public function student_attendances(Student $student){
        $attendances = Attendance::where('student_id', $student->id)->whereMonth('created_at', Carbon::now('Asia/Manila')->format('m'))->get();
        $work_time_total = Attendance::where('student_id', $student->id)->select(DB::raw("SUM(work_time) as total"))->first();
        return [
            'attendances' => $attendances,
            'work_time_total' => $work_time_total->total
        ];
    }

    public function enter(Request $request, Student $student){
        $exists = Attendance::where('student_id', $student->id)->whereDate('created_at', '>=', Carbon::today('Asia/Manila'))->exists();
        if($exists){
            abort(403, 'Student already enter!');
        }

        $attendance = $student->attendances()->create([
            'time_in' => Carbon::now('Asia/Manila')->subHour(),
            'policy' => $request->policy ?? false,
            'late_time' => $request->policy ? Carbon::now()->floatDiffInHours(Carbon::today()->addHours(9)) : null,
        ]);

        if($request->policy){
            $student->remaining += Carbon::now()->floatDiffInHours(Carbon::today()->addHours(9)) * 2;
            $student->save();
        }

        return [
            'attendance' => $attendance,
            'remaining' => $student->remaining,
        ];
    }

    public function leave(Request $request, Student $student, Attendance $attendance){
        $now = now();
        $work_time = Carbon::parse($now)->floatDiffInHours(Carbon::parse($attendance->time_in));
        $attendance->time_out = $now;
        $attendance->work_time = $work_time;
        $attendance->save();
        $attendance->student->remaining -= $work_time;
        $attendance->student->save();

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
