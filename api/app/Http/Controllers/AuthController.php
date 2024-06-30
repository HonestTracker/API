<?php

namespace App\Http\Controllers;


use App\Http\Requests\ApiLoginRequest;
use App\Http\Requests\ApiRegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
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
        $rules = [];

        if ($request->input('device') === 'web') {
            $rules['name'] = 'required|string';
        }
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
        } else {
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
        // Validate the request input, including the image file
        $request->validate([
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // adjust max size as needed
            'name' => 'required|string|max:255',
            'user_id' => 'required|integer',
            'password' => 'required|string',
        ]);

        // Get the authenticated user
        $user = User::where("id", $request->user_id)->first();

        // Create a folder path based on the user's ID or username
        $folder = 'users/' . $user->id;

        // Store the file in the specified folder within the 'public' disk
        $path = $request->file('picture')->store($folder, 'public');

        // Generate a URL for accessing the stored image
        $url = Storage::url($path);

        // Find the user by the provided user_id
        $userToUpdate = User::find($request->user_id);

        // Update user's profile picture path and name in the database
        $userToUpdate->picture_url = $url; // Storing the URL instead of the path
        $userToUpdate->name = $request->name;
        $userToUpdate->update();

        // Attempt to authenticate with the provided credentials
        $credentials = ['email' => $userToUpdate->email, 'password' => $request->password];
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Generate the token with a long TTL
        $token = auth('api')->setTTL(100 * 365 * 24 * 60)->attempt($credentials);

        // Return the response with the token information
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    public function user()
    {
        return response()->json([
            "user" => auth()->user(),
        ]);
    }
    public function edit(Request $request)
    {
        $request->validate([
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // adjust max size as needed
            'name' => 'nullable|string|max:255',
            'user_id' => 'required|integer',
            'email' => 'nullable|email',
        ]);
        $user = auth()->user();
        return response()->json($user);
        if ($request->name) {
            $user->name = $request->name;
        }
        if ($request->email) {
            $user->email = $request->email;
        }
        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $picturePath = $picture->store('profile_pictures', 'public'); // Store the image in storage/app/public/profile_pictures
            $user->picture_url = $picturePath;
        }
        $user->update();
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
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }
    public function handleGoogleCallback(Request $request)
    {
        try {
            $idToken = $request->input('token');
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($idToken);

            $user = User::where('google_id', $googleUser->id)->orWhere('email', $googleUser->email)->first();

            if ($user) {
                // If the user exists, update the Google ID
                if ($user->google_id === null) {
                    $user->google_id = $googleUser->id;
                    $user->save();
                }
            } else {
                // If the user does not exist, create a new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(uniqid()), // Generate a random password
                    'picture_url' => $googleUser->avatar,
                ]);
            }

            // Generate a JWT token for the user
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json(['error' => 'Could not create token'], 500);
            }

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'user' => $user,
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => 'Could not authenticate with Google', 'message' => $e->getMessage()], 500);
        }
    }
}
