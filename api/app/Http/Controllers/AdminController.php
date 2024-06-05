<?php

namespace App\Http\Controllers;


use App\Models\Category;
use App\Models\CategorySite;
use App\Models\Product;
use App\Models\User;
use App\Policies\AdminPolicy;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }
    public function index_users()
    {
        $users = User::orderBy('created_at', 'DESC')->paginate(8);
        return view('admin.users.index', compact('users'));
    }
    public function index_categories()
    {
        $categories = Category::orderBy('created_at', 'DESC')->paginate(8);
        return view('admin.categories.index', compact('categories'));
    }
    public function index_products()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(8);
        return view('admin.products.index', compact('products'));
    }
    public function index_sites(Category $category)
    {
        return view('admin.categories.sites.index', compact('category'));
    }
}
