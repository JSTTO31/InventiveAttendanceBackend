<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'work_time' => 'integer'
    ];


    public function student(){
        return $this->belongsTo(Student::class);
    }

}
