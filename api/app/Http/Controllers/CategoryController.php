<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Models\CategorySite;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        DB::transaction(function () use ($category) {
            // Get all related CategorySite records
            $sites = $category->sites;

            foreach ($sites as $site) {
                // Get all related Product records for each site
                $products = $site->products;

                foreach ($products as $product) {
                    // Delete related ProductPrice records for each product
                    ProductPrice::where('product_id', $product->id)->delete();

                    // Delete the product itself
                    $product->delete();
                }

                // Delete the site itself
                $site->delete();
            }

            // Finally, delete the category
            $category->delete();
        });

        return redirect('/admin/categories')->with('success', "Category deleted!");
    }
}
