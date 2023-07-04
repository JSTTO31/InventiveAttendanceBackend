<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'time_in'  ,
        'time_out',
        'late_time',
        'work_time' ,
        'is_absent' ,
        'policy',
        'created_at',
        'student_id',
        'is_event'
    ];
    protected $casts = [
        'work_time' => 'integer'
    ];


    public function student(){
        return $this->belongsTo(Student::class);
    }

}
