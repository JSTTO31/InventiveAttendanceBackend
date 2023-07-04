<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function getAll(){
        $categories = Category::with('sub_categories.courses')->get();
        return $categories->map(function($category){
            $category = collect($category);
            return [
                ...$category,
                'sub_categories' => collect($category['sub_categories'])->map(function($sub_category){
                    return [...$sub_category, 'courses' => collect(($sub_category['courses']))->map(function($course) use ($sub_category){
                        unset($sub_category['courses']);
                        return [...$course, 'sub_category' => $sub_category];
                    })];
                }),
            ];
        });
    }

    public function create(){
        $request = request();

        $request->validate([
            'name' => 'required',
        ]);
        $category = Category::create(['name' => $request->name]);

        return $category;
    }

    public function get(Category $category){
        return $category;
    }

    public function edit(Category $category){
        $request = request();

        $request->validate([
            'name' => 'required'
        ]);

        $category = Category::where('id', $category->id)->update(['name' => $request->name]);

        return $category;
    }
}
