<?php

namespace App\Helpers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostHelpers
{
    /**
     * Get paginated posts with user relationship
     */
    public static function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);
        $search = $request->get('search');

        $query = Post::with('user');

        // Add search functionality for title column
        if ($search) {
            $query->where('title', 'LIKE', '%' . $search . '%');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Create a new post
     */
    public static function store(Request $request)
    {
        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => $request->user()->id,
        ]);

        return $post->load('user');
    }

    /**
     * Validate post data
     */
    public static function validateStore(Request $request)
    {
        return Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
    }

    /**
     * Get a specific post by ID
     */
    public static function show(string $id)
    {
        return Post::with('user')->find($id);
    }

    /**
     * Update a specific post
     */
    public static function update(Request $request, string $id)
    {
        $post = Post::find($id);
        $post->update($request->only(['title', 'content']));
        return $post->load('user');
    }

    /**
     * Validate update data
     */
    public static function validateUpdate(Request $request)
    {
        return Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);
    }

    /**
     * Check if user can update post
     */
    public static function canUpdate(Post $post, $userId)
    {
        return $post->user_id === $userId;
    }

    /**
     * Delete a specific post
     */
    public static function destroy(Request $request, string $id)
    {
        $post = Post::find($id);
        $post->delete();
        return true;
    }

    /**
     * Check if user can delete post
     */
    public static function canDelete(Post $post, $userId)
    {
        return $post->user_id === $userId;
    }

}
