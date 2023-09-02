<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendeeController extends Controller
{
    public function store(Request $request, Event $event){
        $event->load('date_time');
        $data = collect($request->students)->map(function ($student) use ($event){
            return [
                'student_id' => $student['id'],
                'event_id' => $event->id,
                'is_event' => true,
                'created_at' => $event->date_time->event_date_start,
                'updated_at' => $event->date_time->event_date_start,
            ];
        });

        Attendance::insert([...$data]);


        return Student::whereIn('id', $data->map(fn($student) => $student['student_id']))->get();
    }

    public function removeAttendee(Request $request, Event $event, Student $student){

        Attendance::where('event_id', $event->id)->where('student_id', $student->id)->delete();

        return response(204);
    }
}
