<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\PostHelpers;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of posts
     *
     * @group Posts
     *
     * Get a paginated list of all posts with their associated users.
     * This endpoint requires authentication.
     *
     * @authenticated
     *
     * @queryParam page integer Page number for pagination. Default: 1. Example: 2
     * @queryParam per_page integer Number of posts per page. Default: 15. Example: 10
     * @queryParam search string Search posts by title. Example: Laravel
     *
     * @response array{ status_code: int, data: array{ data: array{ id: int, title: string, content: string, user_id: int, created_at: string, updated_at: string, user: array{ id: int, name: string, email: string } }[], current_page: int, per_page: int, total: int, last_page: int, from: int, to: int, first_page_url: string, last_page_url: string, next_page_url: string, prev_page_url: string, path: string, links: array{ url: string, label: string, active: bool }[] }, message: string }
     *
     * @response array{ status_code: int, errors: string[], settings: array{} } scenario="unauthenticated"
     */
    public function index(Request $request)
    {
        $posts = PostHelpers::index($request);
        return response()->success($posts, 'Posts retrieved successfully');
    }

    /**
     * Create a new post
     *
     * @group Posts
     *
     * Create a new post with the provided title and content.
     * The post will be associated with the authenticated user.
     * This endpoint requires authentication.
     *
     * @authenticated
     *
     * @bodyParam title string required The post title. Example: My First Post
     * @bodyParam content string required The post content. Example: This is the content of my first post.
     *
     * @response array{ status_code: int, data: array{ id: int, title: string, content: string, user_id: int, created_at: string, updated_at: string, user: array{ id: int, name: string, email: string } }, message: string }
     *
     * @response array{ status_code: int, errors: string[], settings: array{} } scenario="unauthenticated"
     *
     * @response array{ status_code: int, errors: array<string, string[]>, settings: array{} } scenario="validation_error"
     */
    public function store(Request $request)
    {
        $validator = PostHelpers::validateStore($request);

        if ($validator->fails()) {
            return response()->unprocessableEntity($validator->errors());
        }

        $post = PostHelpers::store($request);
        return response()->created($post, 'Post created successfully');
    }

    /**
     * Display a specific post
     *
     * @group Posts
     *
     * Get a specific post by its ID with the associated user information.
     * This endpoint requires authentication.
     *
     * @authenticated
     *
     * @urlParam id string required The post ID. Example: 1
     *
     * @response array{ status_code: int, data: array{ id: int, title: string, content: string, user_id: int, created_at: string, updated_at: string, user: array{ id: int, name: string, email: string } }, message: string }
     *
     * @response array{ status_code: int, errors: string[], settings: array{} } scenario="unauthenticated"
     *
     * @response array{ status_code: int, errors: string[], settings: array{} } scenario="not_found"
     */
    public function show(string $id)
    {
        $post = PostHelpers::show($id);

        if (!$post) {
            return response()->notFound(['Post not found']);
        }

        return response()->success($post, 'Post retrieved successfully');
    }

    /**
     * Update a specific post
     *
     * @group Posts
     *
     * Update a specific post. Users can only update their own posts.
     * This endpoint requires authentication.
     *
     * @authenticated
     *
     * @urlParam id string required The post ID. Example: 1
     * @bodyParam title string The post title. Example: Updated Post Title
     * @bodyParam content string The post content. Example: Updated post content.
     *
     * @response array{ status_code: int, data: array{ id: int, title: string, content: string, user_id: int, created_at: string, updated_at: string, user: array{ id: int, name: string, email: string } }, message: string }
     *
     * @response array{ status_code: int, errors: string[], settings: array{} } scenario="unauthenticated"
     *
     * @response array{ status_code: int, errors: string[], settings: array{} } scenario="forbidden"
     *
     * @response array{ status_code: int, errors: string[], settings: array{} } scenario="not_found"
     *
     * @response array{ status_code: int, errors: array<string, string[]>, settings: array{} } scenario="validation_error"
     */
    public function update(Request $request, string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->notFound(['Post not found']);
        }

        if (!PostHelpers::canUpdate($post, $request->user()->id)) {
            return response()->forbidden(['You can only update your own posts']);
        }

        $validator = PostHelpers::validateUpdate($request);

        if ($validator->fails()) {
            return response()->unprocessableEntity($validator->errors());
        }

        $updatedPost = PostHelpers::update($request, $id);
        return response()->success($updatedPost, 'Post updated successfully');
    }

    /**
     * Delete a specific post
     *
     * @group Posts
     *
     * Delete a specific post. Users can only delete their own posts.
     * This endpoint requires authentication.
     *
     * @authenticated
     *
     * @urlParam id string required The post ID. Example: 1
     *
     * @response array{ status_code: int, data: array{}, message: string }
     *
     * @response array{ status_code: int, errors: string[], settings: array{} } scenario="unauthenticated"
     *
     * @response array{ status_code: int, errors: string[], settings: array{} } scenario="forbidden"
     *
     * @response array{ status_code: int, errors: string[], settings: array{} } scenario="not_found"
     */
    public function destroy(Request $request, string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->notFound(['Post not found']);
        }

        if (!PostHelpers::canDelete($post, $request->user()->id)) {
            return response()->forbidden(['You can only delete your own posts']);
        }

        PostHelpers::destroy($request, $id);
        return response()->success([], 'Post deleted successfully');
    }

}
