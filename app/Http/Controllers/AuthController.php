<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $tokenResult = $request->user()->createToken('authToken', ['*'], now()->addDays(15))->plainTextToken;
        return response()->json(['access_token' => $tokenResult]);
    }

    public function logout()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Not Authorized'], 401);
        }
        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'user logged out successfully.']);
    }
}
