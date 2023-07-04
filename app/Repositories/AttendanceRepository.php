<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceRepository
{
    public function getAll(){
        return Attendance::latest()->get();
    }

    public function getStudentAttendances($student_id){
        $attendances = Attendance::where('student_id', $student_id)
        ->select('attendances.*', DB::raw("strftime('%m', created_at) AS month"))
        ->get();

        $work_time_total = Attendance::where('student_id', $student_id)->select(DB::raw("SUM(work_time) as total"))->first();

        $late_time_total = Attendance::where('student_id', $student_id)->select(DB::raw("SUM(late_time) as total"))->first();

        return [
            'attendances' => $attendances,
            'monthly_attendances' => $attendances->groupBy('month'),
            'work_time_total' => $work_time_total->total ?? 0,
            'late_time_total' => $late_time_total->total * 2 ?? 0,
        ];
    }

    public function login(Student $student){
        $request = request();
        $exists = Attendance::where('student_id', $student->id)->whereDate('created_at', '>=', Carbon::today('Asia/Manila'))->exists();
        if($exists){
            abort(403, 'Student already enter!');
        }

        $attendance = $student->attendances()->create([
            'time_in' => Carbon::now('Asia/Manila'),
            'policy' => $request->policy ?? false,
            'late_time' => $request->policy ? $this->getLateTime(now()) : null,
        ]);


        return [
            'attendance' => $attendance,
            'remaining' => $student->remaining,
        ];
    }

    public function logout(Student $student, Attendance $attendance){
        $now = Carbon::today()->addHours(18);
        $attendance->time_out = $now;
        $attendance->work_time = $this->getWorkTime($attendance->time_in, $now);
        $attendance->save();

        return $attendance;
    }

    public function absent(Student $student){
        $exists = Attendance::where('student_id', $student->id)->whereDate('created_at', '>=', Carbon::today())->exists();

        if($exists){
            abort(403, 'Student already have record!');
        }

        $attendance = $student->attendances()->create([
            'is_absent' => true,
        ]);

        return $attendance;
    }


    public function manual(Student $student){
        $request = request();
        $currentDate = Carbon::parse($request->time_in, 'Asia/Manila')->format('Y-m-d');
        $attendance = Attendance::where('student_id', $student->id)->whereDate('created_at', $currentDate)->first();

        if($attendance){
            $attendance->update([
                'time_in' => $request->option != 'absent' ? $request->time_in : null,
                'time_out' =>  $request->option != 'absent' ? $request->time_out : null,
                'late_time' =>  $request->option != 'absent' && $request->option == 'policy' ? $this->getLateTime($request->time_in) : null,
                'work_time' =>  $request->option != 'absent' ? $this->getWorkTime($request->time_in, $request->time_out) : null,
                'is_absent' => $request->option != 'absent' ? false : true,
                'policy' => $request->option == 'policy' ? true : false,
            ]);

            return $attendance;
        }else{
            $attendance = $student->attendances()->create([
                'time_in' => $request->option != 'absent' ? $request->time_in : null,
                'time_out' =>  $request->option != 'absent' ? $request->time_out : null,
                'late_time' =>  $request->option != 'absent' && $request->option == 'policy' ? $this->getLateTime($request->time_in) : null,
                'work_time' =>  $request->option != 'absent' ? $this->getWorkTime($request->time_in, $request->time_out) : null,
                'is_absent' => $request->option != 'absent' ? false : true,
                'policy' => $request->option == 'policy' ? true : false,
                'created_at' => $request->time_in
            ]);

            return $attendance;
        }
    }

    public function getLateTime($time_in){
        $declaredTime = Carbon::parse($time_in)->setHours(9)->setMinutes(0);
        $timeInDate = Carbon::parse($time_in)->floatDiffInHours(Carbon::parse($declaredTime));

        return $timeInDate * 2;
    }

    public function getWorkTime($time_in, $time_out){
        return Carbon::parse($time_out)->floatDiffInHours(Carbon::parse($time_in));
    }

}
