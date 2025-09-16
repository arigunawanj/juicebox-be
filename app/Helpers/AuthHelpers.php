<?php

namespace App\Helpers;

use App\Models\User;
use App\Jobs\SendWelcomeEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthHelpers
{
    /**
     * Register a new user
     */
    public static function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Dispatch welcome email job
        SendWelcomeEmailJob::dispatch($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Validate registration data
     */
    public static function validateRegister(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
    }

    /**
     * Login user
     */
    public static function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return null; // Return null if login fails
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Validate login data
     */
    public static function validateLogin(Request $request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
    }

    /**
     * Logout user
     */
    public static function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return true;
    }

    /**
     * Get current authenticated user
     */
    public static function me(Request $request)
    {
        return $request->user();
    }

}
