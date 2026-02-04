<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
   public function register(Request $request)
    {
        $request->validate([
          'name' => 'required|string|max:255',
          'email' => 'required|string|email|max:255|unique:users',
          'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ]);
        
        Log::create([
         'user_id' => $user->id,
         'action' => 'User registered',
         'endpoint' => $request->path(),
         'ip_address' => $request->ip(),
        ]);


        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }


    public function login(Request $request)
    {
         $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = JWTAuth::fromUser($user);

        Log::create([
          'user_id' => $user->id,
          'action' => 'User logged in',
          'endpoint' => $request->path(),
          'ip_address' => $request->ip(),
        ]);


         return response()->json([
          'access_token' => $token,
          'token_type' => 'bearer',
          'expires_in' => JWTAuth::factory()->getTTL() * 60,
          'user' => $user
         ]);
    }

    public function logout()
    {
        $user = JWTAuth::parseToken()->authenticate();

        Log::create([
            'user_id' => $user->id,
            'action' => 'User logged out',
            'endpoint' => request()->path(),
            'ip_address' => request()->ip(),
        ]);
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }

}
