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

    public function getTodayAttendances(){
        return Attendance::whereDate('created_at', Carbon::now())->get();
    }

    public function getWeeklyAttendances(){
        $date_end = now();
        $date_start = now()->subDay($date_end->dayOfWeek);

        return Attendance::whereDate('created_at', '>=', $date_start)->whereDate('created_at', '<=', $date_end)->orderBy('created_at', 'ASC')->get();
    }

    public function getStudentAttendances($student_id){
        $attendances = Attendance::with(['event.date_time'])->where('student_id', $student_id)
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
        $exists = Attendance::where('student_id', $student->id)->whereDate('created_at', Carbon::today('Asia/Manila'))->exists();
        if($exists){
            abort(403, 'Student already enter!');
            return;
        }

        $attendance = $student->attendances()->create([
            'time_in' => Carbon::now('Asia/Manila'),
            'policy' => $request->policy ?? false,
            'late_time' => $request->policy ? $this->getLateTime(now()) : null,
            'event_id' => null,

        ]);

        return [
            'attendance' => $attendance,
            'remaining' => $student->remaining,
        ];
    }

    public function logout(Student $student, Attendance $attendance){
        $attendance->time_out = now('Asia/Manila');
        $attendance->work_time = !$attendance->work_time ? $this->getWorkTime($attendance->time_in, now('Asia/Manila')) : $attendance->work_time + $this->getWorkTime($attendance->time_in, now('Asia/Manila'));
        $attendance->save();

        $this->markAsCompleted($student->id);

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


        if($attendance && !$request->allow_relogin){
            $attendance->time_in = $request->option != 'absent' ? $time_in : null;
            $attendance->time_out = $request->option != 'absent' ? $time_out : null;
            $attendance->work_time = $request->option != 'absent' ? $this->getWorkTime($time_in, $time_out) : null;
            $attendance->is_absent = $request->option != 'absent' ? false : true;
            $attendance->policy = $request->option == 'policy' ? true : false;
            $attendance->late_time = $request->option != 'absent' && $request->option == 'policy' ? $this->getLateTime($time_in) : null;
            $attendance->event_id = null;
            $attendance->save();

            $this->markAsCompleted($student->id);


            return $attendance;
        }else{
            $attendance = $student->attendances()->create([
                'time_in' => $request->option != 'absent' ? $time_in : null,
                'time_out' =>  $request->option != 'absent' ? $time_out : null,
                'work_time' =>  $request->option != 'absent' ? (float)$this->getWorkTime($time_in, $time_out) : null,
                'is_absent' => $request->option != 'absent' ? false : true,
                'policy' => $request->option == 'policy' ? true : false,
                'created_at' => $time_in,
                'late_time' => $request->option != 'absent' && $request->option == 'policy' ? $this->getLateTime($time_in) : null,
                'event_id' => null,
            ]);

            $this->markAsCompleted($student->id);


            return $attendance;
        }
    }

    public function manual_remove(Student $student, $request){
        Attendance::whereDate('created_at', Carbon::parse($request->time_in))->where('student_id', $student->id)->delete();

        return response(204);
    }

    public function getLateTime($time_in){
        $declaredTime = 9.0;
        $timeIn = (float)Carbon::parse($time_in, 'Asia/Manila')->format('H.i');

        return ($timeIn - $declaredTime);
    }


    function getWorkTime($time_in, $time_out){
        $request = request();

        $time_in = Carbon::parse($time_in);

        if($request->option == 'policy'){
            $time_in = $time_in->addMinutes((int)substr(number_format($this->getLateTime($time_in), 2, '.', ''), 2));
        }

        $time_out = Carbon::parse($time_out);

        $time_in_hours = ((float) $time_in->format('H')) * 60;
        $time_in_minutes = (float)$time_in_hours + (float) $time_in->format('i');

        $time_out_hours = ((float) $time_out->format('H')) * 60;
        $time_out_minutes = (float)$time_out_hours + (float) $time_out->format('i');

        $hours =  (int)(($time_out_minutes - $time_in_minutes) / 60);
        $minutes =  (($time_out_minutes - $time_in_minutes) % 60);

        $work_time = (float)Carbon::now()->setHours($hours)->setMinutes($minutes)->format('H.i');


        if(!$request->break){
            return $work_time;
        }

        return $work_time - 1;
    }

    public function relogin(Student $student, Attendance $attendance){
        $attendance = $student->attendances()->create([
            'time_in' => Carbon::now('Asia/Manila'),
            'created_at' => now(),
        ]);

        return $attendance;
    }

    public function markAsCompleted($student_id){
        $student = Student::where('id', $student_id)->withSum('attendances as work_time_total', 'work_time')->first();
        if($student->work_time_total >= $student->remaining){
            $student->completed_at = now();
        }
    }

}
