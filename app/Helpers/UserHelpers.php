<?php

namespace App\Helpers;

use App\Models\User;

class UserHelpers
{
    /**
     * Get a specific user by ID
     */
    public static function show(string $id)
    {
        return User::with('posts')->find($id);
    }
}
