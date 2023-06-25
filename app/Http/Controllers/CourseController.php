<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::all();
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
            'description' => 'required',
            'image' => ['required', 'mimes:png,jpg'],
        ]);

        $image = $request->file('image')->store('courses', 'public');Z
        $url = URL::to('/storage/' . $image);

        $course = $subCategory->courses()->create([
             'name' => $request->name,
             'number_of_session' => $request->number_of_session,
             'description' => $request->description,
             'image' => $url,
            ]);

        return $course;

    //     $course = Course::create([
    //     'name' => $request->name,
    //     'number_of_session' => $request->number_of_session,
    //     'description' => $request->description
    // ]);
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
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required',
            'number_of_session' => 'number_of_session',
            'description' => 'description'
        ]);
        $course = Course::where('id', $course->id)->update(['name' => $request->name, 'number_of_session' => $request->number_of_session, 'description' => $request->description]);

        return $course;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();
    }
}
