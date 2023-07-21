<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany as RelationsHasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public static function boot(){
        parent::boot();

        self::deleting(function(Category $category){
            $category->sub_categories()->delete();
        });

    }


    public function sub_categories(): RelationsHasMany
    {
        return $this->hasMany(SubCategory::class);
    }


}
