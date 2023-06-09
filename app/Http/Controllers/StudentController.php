<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestStudent;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index()
    {
        return Student::with('attendance')->get();
    }

    public function show(Request $request, Student $student){
        return $student->load('attendance');
    }

    public function currentOJTs(Request $request){
        return Student::with('attendance')->get();
    }

    public function store(RequestStudent $request)
    {
        $base_path = $request->getSchemeAndHttpHost() . '/storage/profiles/';
        $image = $request->gender == 'male' ? $base_path . 'default-male.png' : $base_path . 'default-female.png';
        $student = Student::create([...$request->only(['first_name', 'last_name', 'email', 'phone_number', 'school_name', 'school_year', 'address', 'course', 'gender']), 'image' => $image]);

        return $student;
    }

    public function update(RequestStudent $request, Student $student)
    {
        $student = Student::where('id', $student->id)->update($request->only(['first_name', 'last_name', 'email', 'phone_number', 'school_name', 'school_year', 'address', 'course']));

        return $student;
    }

    public function destroy(Request $request, Student $student)
    {
        $student->delete();
        return response(null);
    }
}
