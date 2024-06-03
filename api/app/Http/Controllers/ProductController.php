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
    public function change_product_name()
    {
        $products = Product::all();
        foreach($products as $product)
        {
            $new_name =  explode(' - ', $product->name, 2)[0];
            return $new_name;
        }
    }
}
