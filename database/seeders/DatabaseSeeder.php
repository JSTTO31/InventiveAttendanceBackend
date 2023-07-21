<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Student::factory(10)->create();
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // \App\Models\Attendance::whereDate('created_at', '>=', Carbon::today())->delete();
        // Attendance::factory()->count(10)->create();


        // \App\Models\Attendance::fac

        // \App\Models\Student::all()->each(function(Student $student){
        //     $path =  Str::replace('192.168.100.107', 'localhost',$student->image);

        //     $student->update([
        //         'image' => $path,
        //     ]);
        // });

        // \App\Models\Course::all()->each(function(Course $course){
        //     // $path =  Str::replace('http://192.168.254.162:8000/', 'https://www.inventivemedia.com.ph/ojt/', $course->image);
        //     $path =  Str::replace('http://192.168.100.107:8000', 'https://localhost:8000', $course->image);

        //     $course->update([
        //         'image' => $path,
        //     ]);
        // });
    }
}
