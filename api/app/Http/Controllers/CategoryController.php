<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Models\CategorySite;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function create_admin()
    {
        return view("admin.categories.create");
    }
    public function store_admin(StoreCategoryRequest $request)
    {
        $category = new Category();
        $category->name = $request->name;
        $category->save();
        return redirect('/admin/categories')->with('success', "Category created!");
    }
    public function edit_admin(Category $category)
    {
        return view("admin.categories.edit", compact('category'));
    }
    public function update_admin(StoreCategoryRequest $request, Category $category)
    {
        $category->name = $request->name;
        $category->update();
        return redirect('/admin/categories')->with('success', "Category updated!");
    }
    public function delete_admin(Category $category)
    {
        foreach($category->products as $product)
        return redirect('/admin/categories')->with('success', "Category updated!");
    }
}
