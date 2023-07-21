<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['remaining' => 'integer'];

    public static function boot(){
        parent::boot();

        self::deleting(function(Student $student){
            Attendance::where('student_id', $student->id)->delete();

            $path = Str::replace(url("/storage\/"), '/', $student->image);
            Storage::disk('public')->delete($path);

        });
    }

    public function attendance(){
        return $this->hasOne(Attendance::class)->whereDate('created_at', '>=', Carbon::today())->latest();
    }

    public function attendances(){
        return $this->hasMany(Attendance::class);
    }




}
