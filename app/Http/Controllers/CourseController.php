<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Student $student)
    {
        $courses = Course::whereHas('attendances', fn($query) => $query->where('student_id', $student->id))
        ->with(['sub_category'])
        ->get();
        return $courses;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, SubCategory $subCategory)
    {
        $request->validate([
            'name' => 'required',
            'number_of_session' => ['required', 'min:1', 'max:5'],
            'image' => ['required', 'mimes:png,jpg'],
        ]);

        $image = $request->file('image')->store('courses', 'public');
        $url = URL::to('/storage/' . $image);

        $course = $subCategory->courses()->create([
             'name' => $request->name,
             'number_of_session' => $request->number_of_session,
             'description' => $request->description,
             'image' => $url,
            ]);

        return $course;
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return $course;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubCategory $subCategory, Course $course)
    {
        $request->validate([
            'name' => 'required',
            'number_of_session' => ['required', 'min:1', 'max:5'],
            'sub_category_id' => ['required']
        ]);
        $course = Course::where('id', $course->id)->update($request->only(['name', 'number_of_session', 'sub_category_id', 'description']));

        return $course;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubCategory $subCategory, Course $course)
    {
        $course->delete();

        return response(null);
    }

    public function add_attendee(Request $request, Course $course){
        $request->validate([
            'student_id' => 'required',
            'date' => 'required|date'
        ]);
        $is_attended = Attendance::where('student_id', $request->student_id)->where('course_id', $course->id)->exists();

        if($is_attended){
            abort(403, "Sorry, it's seems the student already attended!");
        }

        $start_date = Carbon::parse($request->date);
        $end_date = Carbon::parse($request->date)->addDays($course->number_of_session);
        Attendance::where('student_id',$request->student_id)
        ->whereDate('created_at', '>=', $start_date)
        ->whereDate('created_at', '<=', $end_date)
        ->delete();

        $attendances = [];

        for($index = 0;$index <= $course->number_of_session - 1;$index++){
            $date = Carbon::parse($start_date)->addDays($index);
            $newAttendance = [
                'is_event' => true,
                'student_id' => $request->student_id,
                'created_at' => $date
            ];
            array_push($attendances, $newAttendance);
        }

        $attendannces = $course->attendances()->createMany($attendances);

        return $attendannces;
    }

    public function remove_attendee(Request $request, Course $course, Student $student){
        Attendance::where('course_id', $course->id)->where('student_id', $student->id)->delete();

        return response(null);
    }
}
