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
        $delete_check = CategorySite::where('category_id', $category->id)->get();
        if($delete_check)
        {
            foreach($delete_check as $product)
            {
                foreach($product->prices as $price)
                {
                    $price->delete();   
                }
                foreach($product->comments as $comment)
                {
                    $comment->delete();
                }
                foreach($product->favorites as $favorite)
                {
                    $favorite->delete();
                }
                $product->delete();
            }
        }

        return redirect('/admin/categories')->with('success', "Category deleted!");
    }
}
