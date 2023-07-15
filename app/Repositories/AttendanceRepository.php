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
        ->select('attendances.*', DB::raw("month(created_at) AS month"))
        ->get();

        $work_time_total = Attendance::where('student_id', $student_id)->select(DB::raw("SUM(work_time) as total"))->first();

        $late_time_total = Attendance::where('student_id', $student_id)->select(DB::raw("SUM(late_time) as total"))->first();

        return [
            'attendances' => $attendances,
            'monthly_attendances' => $attendances->groupBy('month'),
            'work_time_total' => $work_time_total->total ?? 0,
            'late_time_total' => $late_time_total->total ?? 0,
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
        $attendance->time_out = now('Asia/Manila');
        $attendance->work_time = $this->getWorkTime($attendance->time_in, now('Asia/Manila'));
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
        $time_in = Carbon::parse($request->time_in)->setTimezone('Asia/Manila');
        $time_out = Carbon::parse($request->time_out)->setTimezone('Asia/Manila');


        if($attendance){
            $attendance->update([
                'work_time' =>  $request->option != 'absent' ? $this->getWorkTime($request->time_in, $request->time_out) : null,
                'time_in' => $request->option != 'absent' ? $time_in : null,
                'time_out' =>  $request->option != 'absent' ? $time_out : null,
                'is_absent' => $request->option != 'absent' ? false : true,
                'policy' => $request->option == 'policy' ? true : false,
            ]);

            $attendance->late_time = $request->option != 'absent' && $request->option == 'policy' ? $this->getLateTime($attendance->time_in) : null;


            $attendance->save();

            return $request->time_in;
        }else{
            $attendance = $student->attendances()->create([
                'time_in' => $request->option != 'absent' ? $time_in : null,
                'time_out' =>  $request->option != 'absent' ? $time_out : null,
                'work_time' =>  $request->option != 'absent' ? $this->getWorkTime($request->time_in, $request->time_out) : null,
                'is_absent' => $request->option != 'absent' ? false : true,
                'policy' => $request->option == 'policy' ? true : false,
                'created_at' => $request->time_in
            ]);

            $attendance->late_time = $request->option != 'absent' && $request->option == 'policy' ? $this->getLateTime($attendance->time_in) : null;
            $attendance->save();

            // $attendance->reset();

            return $attendance;
        }
    }

    public function getLateTime($time_in){
        $declaredTime = 9.0;
        $timeIn = (float)Carbon::parse($time_in, 'Asia/Manila')->format('H.i');

        return ($timeIn - $declaredTime);
    }

    public function getWorkTime($time_in, $time_out){
        $time_in = Carbon::parse($time_in);
        $difference = Carbon::parse($time_out)->diff($time_in);
        $minutes = strlen(((string)$difference->i)) > 1 ? (string)$difference->i : '0' . ((string)$difference->i);
        $work_time = (float)($difference->h . '.' . $minutes);

        if((int)Carbon::parse($time_in)->format('M') >= 12){
            return $work_time;
        }

        return $work_time - 1;
    }

}
