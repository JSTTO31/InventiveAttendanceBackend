<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $category = Category::create(['name' => $request->name]);
        return $category;
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $category;
    }

    /**
     * Update the specified resou rce in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required'
        ]);
        $category = Category::where('id', $category->id)->update(['name' => $request->name]);
        return $category;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
    }
}
