<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public $category_repository;

    public function __construct()
    {
        $this->category_repository = new CategoryRepository();
    }

    public function index()
    {
       return $this->category_repository->getAll();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       return $this->category_repository->create();
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $this->category_repository->get($category);
    }

    /**
     * Update the specified resou rce in storage.
     */
    public function update(Request $request, Category $category)
    {
        return $this->category_repository->edit($category);
    }


    public function destroy(Category $category)
    {
        $category->delete();

        return response(null);
    }
}
