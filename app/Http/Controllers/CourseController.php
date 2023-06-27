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
}
