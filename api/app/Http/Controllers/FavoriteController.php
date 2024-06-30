<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index_web(Request $request)
    {
        $user = auth()->user();
        $favorites = Favorite::where('user_id', $user->id)->get();
        return response()->json([
            "user" => $user,
            "favorites" => $favorites,
        ]);
    }
    public function store(Request $request)
    {
        $user = auth()->user();
        $product = Product::where('id', $request->product_id)->first();
        $check = Favorite::where('user_id', $user->id)->where('product_id', $request->product_id)->first();
        if($check !== null)
        {
            $check->delete();
            $favorites = Favorite::where('user_id', $user->id)->get();
            return response()->json([
                "message" => "Removed!",
                "user" => $user,
                "favorites" => $favorites,
            ]);
        }
        $favorite = new Favorite;
        $favorite->user_id = $user->id;
        $favorite->product_id = $product->id;
        $favorite->save();
        $favorites = Favorite::where('user_id', $user->id)->get();
        return response()->json([
            "message" => "Added!",
            "user" => $user,
            "favorites" => $favorites,
        ]);
    }
}
