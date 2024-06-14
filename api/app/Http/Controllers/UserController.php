<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function admin_login(Request $request)
    {
        //Kijken of de ingevoerde gegevens gelijk staan aan de database
        $credentials = request(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            //Als verificatie faalt wordt er een 401 error gegooid
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        //Als verificatie slaagt wordt er een JWT-token teruggegeven
        return view('admin.index');
    }
    public function delete(User $user)
    {
        foreach($user->comments as $comment)
        {
            $comment->delete();
        }
        $user->delete();
        return redirect('/admin/users')->with('success', "User deleted!");
    }
}
