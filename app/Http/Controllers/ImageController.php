<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileImageRequest;
use App\Models\Course;
use App\Models\Student;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
class ImageController extends Controller
{
    public function changeProfile(ProfileImageRequest $request, Student $student){
        $image = $request->file('image')->store('profiles', 'public');
        $url = URL::to('/storage/' . $image);


        if(!str_contains($student->image, 'default-male.png') && !str_contains($student->image, 'default-female.png')){
            $path = Str::replace(URL::to('/storage/'), '', $student->image);
            Storage::disk('public')->delete($path);
        }

        $student->image = $url;
        $student->save();

        return $url;
    }

    public function changeCourseImage(Request $request, SubCategory $subCategory, Course $course){
        $request->validate(['image' => ['required', 'mimes:png,jpg']]);
        $image = $request->file('image')->store('courses', 'public');
        $url = URL::to('/storage/' . $image);

        $path = Str::replace(URL::to('/storage/'), '', $course->image);
        Storage::disk('public')->delete($path);

        $course->image = $url;
        $course->save();

        return $url;
    }
}
