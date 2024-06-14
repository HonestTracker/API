<?php

namespace App\Http\Controllers;


use App\Http\Requests\ApiLoginRequest;
use App\Http\Requests\ApiRegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends \Illuminate\Routing\Controller
{
    //API middleware op elke functie in deze controller
    //Hier vallen login en register niet onder zodat een gebruiker hier wel kan komen zonder ingelogd te zijn
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'user_details']]);
    }
    //Functie voor het registeren van een nieuwe gebruiker
    public function register(ApiRegisterRequest $request)
    {
        //Wachtwoord hashen
        $password = Hash::make($request->password);
        //User instantie aanvragen en data invullen
        $user = new User;
        if ($request->device === 'mobile') {
            $user->name = "placeholder";
        } else {
            $user->name = $request->name;
        }
        $user->email = $request->email;
        $user->password = $password;
        $user->picture_url = "images.placeholder";
        $user->save();
        if ($request->device === 'web') {
            $credentials = request(['email', 'password']);
            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            // Set token with standard expiration time for web
            $token = auth('api')->setTTL(config('jwt.ttl'))->attempt($credentials);
            return $this->respondWithToken($token);
        }
        else {
            return response()->json($user);
        }
    }
    //Functie voor het inloggen van een bestaande gebruiker
    public function login(ApiLoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        // Attempt to get a token with the provided credentials
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Adjust token TTL based on device type
        if ($request->device === 'mobile') {
            // Set token with a very long expiration time for mobile
            $token = auth('api')->setTTL(100 * 365 * 24 * 60)->attempt($credentials);
        } else {
            // Set token with standard expiration time for web
            $token = auth('api')->setTTL(config('jwt.ttl'))->attempt($credentials);
        }

        return $this->respondWithToken($token);
    }
    public function user_details(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        $user->name = $request->name;
        $user->update();
        $credentials = ['email' => $user->email, 'password' => $request->password];
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $token = auth('api')->setTTL(100 * 365 * 24 * 60)->attempt($credentials);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->setTTL(100 * 365 * 24 * 60),
        ]);
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
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user(),
        ]);
    }
}
