<?php

namespace App\Http\Controllers;


use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends \Illuminate\Routing\Controller
{
    //API middleware op elke functie in deze controller
    //Hier vallen login en register niet onder zodat een gebruiker hier wel kan komen zonder ingelogd te zijn
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    //Functie voor het registeren van een nieuwe gebruiker
    public function register(Request $request)
    {
        //Wachtwoord hashen
        $request['password'] = Hash::make($request['password']);
        //User instantie aanvragen en data invullen
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();

        return response()->json(['message' => 'Account aangemaakt! Je kan nu inloggen!']);
    }
    //Functie voor het inloggen van een bestaande gebruiker
    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($request->device === 'mobile') {
            // Set token with a very long expiration time for mobile
             $token = auth()->setTTl(100 * 365 * 24 * 60 * 60)->attempt($credentials);
        } else {
            // Set token with standard expiration time for web
            $token = auth()->setTTL(config('jwt.ttl'))->attempt($credentials);
        }
         return $this->respondWithToken($token);
    }
    //Functie voor het uitloggen van een ingelogde gebruiker
    public function logout(Request $request)
    {
        //Doormiddel van de meegegeven token kan Laravel automatisch de gebruiker uitloggen (token invalideren)
        auth()->logout();
        return response()->json(['message' => 'Uitgelogd!']);
    }
    //Functie voor het genereren van een JWT-token
    protected function respondWithToken($token)
    {
        //Token wordt gegenereerd door een composer package, dit gebeurd automatisch
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ]);
    }
}
