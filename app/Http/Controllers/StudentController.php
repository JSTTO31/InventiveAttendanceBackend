<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestStudent;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        // return "index";
        $students = collect(
            Student::with(['attendance' => function($query){
                $query->whereDate('created_at', Carbon::today());
            }])
            // ->where(DB::raw("first_name LIKE '%". $request->search ."%'"))
            ->where('first_name', 'like', '%' . $request->search . '%')
            // ->orWhere('last_name', 'LIKE', '%'.$request->search.'%')
            ->when($request->filter == 'completed', fn($query) => $query->where('remaining', '<', 1))
            ->when(!$request->filter, fn($query) => $query->where('remaining', '>', 0))
            ->when($request->filter == 'all_students', fn($query) => $query)
            ->when($request->course_id, function($query) use ($request){
                $query->whereDoesntHave('attendances', function($query) use ($request) {
                    $query->where('course_id', $request->course_id);
                });
            })
            ->paginate(10)
        );
        $data = $students['data'];
        unset($students['data']);
        return [
            'students' => $data,
            'pageOptions' => $students
        ];
    }

    public function show(Request $request, Student $student){
        // return "show";
        return $student->load(['attendance' => function($query){
            $query->whereDate('created_at', Carbon::today());
        }]);
    }

    public function currentOJTs(Request $request){
        $students = Student::with('attendance')
        ->withCount(['attendances as work_time_total' => fn($query) => $query->select(DB::raw('SUM(attendances.work_time)'))])
        ->where('remaining', '>', 0)
        ->orderBy('work_time_total', 'desc')
        ->limit(5)->get();
        $numberOfStudents = DB::table('students')->select(DB::raw('COUNT(id) as total'))->first();
        $remaining = DB::table('students')->where('remaining', '>', 0)->select(DB::raw('COUNT(id) as total'))->first();
        return [
            'students' => $students,
            'number_of_students' => $numberOfStudents->total,
            'remaining' => $remaining->total
        ];
    }

    public function store(RequestStudent $request)
    {
        $base_path = $request->getSchemeAndHttpHost() . '/storage/profiles/';
        $image = $request->gender == 'male' ? $base_path . 'default-male.png' : $base_path . 'default-female.png';

        $student = Student::create([...$request->only(['first_name', 'last_name', 'email', 'phone_number', 'school_name', 'school_year', 'address', 'course', 'gender', 'remaining', 'position']), 'image' => $image]);

        return $student;
    }

    public function update(RequestStudent $request, Student $student)
    {
        $student = Student::where('id', $student->id)->update($request->only(['first_name', 'last_name', 'email', 'phone_number', 'school_name', 'school_year', 'address', 'course', 'gender', 'remaining', 'position']));

        return $student;
    }

    public function destroy(Request $request, Student $student)
    {
        $student->delete();
        return response(null);
    }

    public function getAllStudentCourses(Request $request, ){

    }
}
