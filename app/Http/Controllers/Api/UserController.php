<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\UserHelpers;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a specific user
     *
     * @group Users
     *
     * Get a specific user by their ID with their associated posts.
     * This endpoint requires authentication.
     *
     * @authenticated
     *
     * @urlParam id string required The user ID. Example: 1
     *
     * @response array{ status_code: int, data: array{ id: int, name: string, email: string, created_at: string, updated_at: string, posts: array{ id: int, title: string, content: string, user_id: int, created_at: string, updated_at: string }[] }, message: string }
     *
     * @response array{ status_code: int, errors: string[], settings: array{} } scenario="unauthenticated"
     *
     * @response array{ status_code: int, errors: string[], settings: array{} } scenario="not_found"
     */
    public function show(string $id)
    {
        $user = UserHelpers::show($id);

        if (!$user) {
            return response()->notFound(['User not found']);
        }

        return response()->success($user, 'User retrieved successfully');
    }
}
