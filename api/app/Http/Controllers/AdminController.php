<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }
    public function index_products()
    {
        $products = Product::paginate(8);
        return view('admin.products.index', compact('products'));
    }
}
