<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = ['remaining' => 'integer'];
    protected $append = ['work_time'];

    public function attendance(){
        return $this->hasOne(Attendance::class)->whereDate('created_at', '>=', Carbon::today())->latest();
    }

    public function attendances(){
        return $this->hasOne(Attendance::class);
    }


}
