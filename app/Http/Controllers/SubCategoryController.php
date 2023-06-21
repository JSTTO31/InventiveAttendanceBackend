<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subCategories = SubCategory::all();
        return $subCategories;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $sub_category = $category->sub_categories()->create(['name' => $request->name]);
        return $sub_category;

        // $subCategory = SubCategory::create(['name' => $request->name]);


    }

    /**
     * Display the specified resource.
     */
    public function show(SubCategory $subCategory)
    {
        return $subCategory;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubCategory $subCategory)
    {
        $request->validate([
            'name' => 'required'
        ]);
        $subCategory = SubCategory::where('id', $subCategory->id)->update(['name' => $request->name]);
        return $subCategory;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubCategory $subCategory)
    {
        $subCategory->delete();
    }
}
