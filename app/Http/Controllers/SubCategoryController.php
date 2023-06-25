<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sub_categories = DB::table('sub_categories')->join('categories', 'sub_categories.category_id', '=', 'categories.id')->select('sub_categories.*', 'categories.name as category_name')->get();
        return $sub_categories;
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
        return [...collect($sub_category), 'category_name' => $category->name];
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
            'name' => 'required',
            'category_id' => 'required|exists:categories,id'
        ]);

        $subCategory = SubCategory::where('id', $subCategory->id)->update([
            'name' => $request->name,
            'category_id' => $request->category_id
        ]);


        return [
            ...collect($subCategory),
            'category_name' => Category::where('id', $request->category_id)->first()->name
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category, SubCategory $subCategory)
    {
        $subCategory->delete();

        return;
    }
}
