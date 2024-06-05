<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function homepage()
    {
        $products = Product::with('prices')->with('site')->get();
        return $products;
    }
}
