<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\AuthHelpers;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * @group Authentication
     *
     * Register a new user account with the provided information.
     * Upon successful registration, a welcome email will be sent to the user
     * and an authentication token will be returned.
     *
     * @bodyParam name string required The user's full name. Example: John Doe
     * @bodyParam email string required The user's email address. Must be unique. Example: john@example.com
     * @bodyParam password string required The user's password. Must be at least 8 characters. Example: password123
     * @bodyParam password_confirmation string required Password confirmation. Must match password. Example: password123
     *
     * @response array{ status_code: int, data: array{ user: array{ id: int, name: string, email: string, created_at: string, updated_at: string }, token: string }, message: string }
     *
     * @response array{ status_code: int, errors: array<string, string[]>, settings: array{} } scenario="validation_error"
     */
    public function register(Request $request)
    {
        $validator = AuthHelpers::validateRegister($request);

        if ($validator->fails()) {
            return response()->unprocessableEntity($validator->errors());
        }

        $data = AuthHelpers::register($request);
        return response()->created($data, 'User registered successfully');
    }

    /**
     * Login user
     *
     * @group Authentication
     *
     * Authenticate a user with email and password.
     * Returns user information and authentication token upon successful login.
     *
     * @bodyParam email string required The user's email address. Example: john@example.com
     * @bodyParam password string required The user's password. Example: password123
     *
     * @response array{ status_code: int, data: array{ user: array{ id: int, name: string, email: string, created_at: string, updated_at: string }, token: string }, message: string }
     *
     * @response array{ status_code: int, errors: string[], settings: array{} } scenario="invalid_credentials"
     *
     * @response array{ status_code: int, errors: array<string, string[]>, settings: array{} } scenario="validation_error"
     */
    public function login(Request $request)
    {
        $validator = AuthHelpers::validateLogin($request);

        if ($validator->fails()) {
            return response()->unprocessableEntity($validator->errors());
        }

        $data = AuthHelpers::login($request);

        if (!$data) {
            return response()->unauthorized(['Invalid credentials']);
        }

        return response()->success($data, 'Login successful');
    }

    /**
     * Logout user
     *
     * @group Authentication
     *
     * Logout the authenticated user by revoking their current access token.
     * This endpoint requires authentication.
     *
     * @authenticated
     *
     * @response array{ status_code: int, data: array{}, message: string }
     *
     * @response array{ status_code: int, errors: string[], settings: array{} } scenario="unauthenticated"
     */
    public function logout(Request $request)
    {
        AuthHelpers::logout($request);
        return response()->success([], 'Logout successful');
    }

    /**
     * Get current user
     *
     * @group Authentication
     *
     * Get the currently authenticated user's information.
     * This endpoint requires authentication.
     *
     * @authenticated
     *
     * @response array{ status_code: int, data: array{ id: int, name: string, email: string, created_at: string, updated_at: string }, message: string }
     *
     * @response array{ status_code: int, errors: string[], settings: array{} } scenario="unauthenticated"
     */
    public function me(Request $request)
    {
        $user = AuthHelpers::me($request);
        return response()->success($user, 'User retrieved successfully');
    }

}
