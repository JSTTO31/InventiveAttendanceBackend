<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManualAttendanceRequest;
use App\Models\Attendance;
use App\Models\Student;
use App\Repositories\AttendanceRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public $attendanceRepository;

    public function __construct(){
        $this->attendanceRepository = new AttendanceRepository();
    }

    public function index(){
        return $this->attendanceRepository->getAll();
    }

    public function student_attendances(Student $student){
       return $this->attendanceRepository->getStudentAttendances($student->id);
    }

    public function enter(Request $request, Student $student){
        return $this->attendanceRepository->login($student);
    }

    public function leave(Request $request, Student $student, Attendance $attendance){
        return $this->attendanceRepository->logout($student, $attendance);
    }

    public function absent(Request $request, Student $student){
        return $this->attendanceRepository->absent($student);
    }

    public function manual(ManualAttendanceRequest $request, Student $student){
       return $this->attendanceRepository->manual($student, $request);
    }
}
