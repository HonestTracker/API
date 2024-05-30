<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function homepage()
    {
        $product = Product::with('prices')->first();
        return $product;
    }
}
