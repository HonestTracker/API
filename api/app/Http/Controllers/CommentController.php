<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
   public function store(StoreCommentRequest $request)
   {
      $user = User::where('id', $request->user_id)->first();
      $product = Product::where('id', $request->product_id)->first();
      $comment = new Comment();
      $comment->user_id = $user->id;
      $comment->product_id = $product->id;
      $comment->text = $request->text;
      $comment->stars = $request->stars;
      $comment->save();
      return response()->json("Saved!");
   }
   public function delete(Request $request)
   {
      $comment = Comment::where('id', $request->comment_id)->first();
      $comment->delete();
      return response()->json("Deleted!");
   }
}
