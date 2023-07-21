<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'number_of_session',
        'description',
        'image'
    ];

    public static function boot(){
        parent::boot();

        self::deleting(function(Course $course){
            $path = Str::replace(url("/storage\/"), '/', $course->image);
            Storage::disk('public')->delete($path);
        });

    }


    public function sub_category(){
        return $this->belongsTo(SubCategory::class);
    }

    public function attendances(){
        return $this->hasMany(Attendance::class);
    }
}
