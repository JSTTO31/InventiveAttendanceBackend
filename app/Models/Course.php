<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'number_of_session',
        'description',
        'image'
    ];


    public function sub_category(){
        return $this->belongsTo(SubCategory::class);
    }
}
