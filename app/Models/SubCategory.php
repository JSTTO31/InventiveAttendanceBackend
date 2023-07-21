<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public static function boot(){
        parent::boot();

        self::deleting(function(SubCategory $sub_category){
            $sub_category->courses()->delete();
        });

    }


    public function courses(){
        return $this->hasMany(Course::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }


}
