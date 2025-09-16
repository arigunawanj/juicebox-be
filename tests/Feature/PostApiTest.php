<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_posts()
    {
        Post::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status_code',
                    'data' => [
                        'data' => [
                            '*' => ['id', 'title', 'content', 'user_id', 'created_at', 'updated_at', 'user']
                        ],
                        'current_page',
                        'per_page',
                        'total'
                    ],
                    'message'
                ]);
    }

    public function test_can_list_posts_with_pagination()
    {
        Post::factory()->count(5)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/posts?page=1&per_page=3');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status_code',
                    'data' => [
                        'data' => [
                            '*' => ['id', 'title', 'content', 'user_id', 'created_at', 'updated_at', 'user']
                        ],
                        'current_page',
                        'per_page',
                        'total',
                        'last_page',
                        'from',
                        'to',
                        'first_page_url',
                        'last_page_url',
                        'next_page_url',
                        'prev_page_url',
                        'path',
                        'links'
                    ],
                    'message'
                ])
                ->assertJsonCount(3, 'data.data')
                ->assertJsonPath('data.current_page', 1)
                ->assertJsonPath('data.per_page', 3);
    }

    public function test_can_search_posts_by_title()
    {
        Post::factory()->create(['user_id' => $this->user->id, 'title' => 'Laravel Tutorial']);
        Post::factory()->create(['user_id' => $this->user->id, 'title' => 'PHP Basics']);
        Post::factory()->create(['user_id' => $this->user->id, 'title' => 'Laravel Advanced']);

        $response = $this->getJson('/api/posts?search=Laravel');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status_code',
                    'data' => [
                        'data' => [
                            '*' => ['id', 'title', 'content', 'user_id', 'created_at', 'updated_at', 'user']
                        ],
                        'current_page',
                        'per_page',
                        'total'
                    ],
                    'message'
                ])
                ->assertJsonCount(2, 'data.data');
    }

    public function test_can_combine_search_and_pagination()
    {
        Post::factory()->create(['user_id' => $this->user->id, 'title' => 'Laravel Tutorial']);
        Post::factory()->create(['user_id' => $this->user->id, 'title' => 'PHP Basics']);
        Post::factory()->create(['user_id' => $this->user->id, 'title' => 'Laravel Advanced']);
        Post::factory()->create(['user_id' => $this->user->id, 'title' => 'Laravel Framework']);

        $response = $this->getJson('/api/posts?search=Laravel&page=1&per_page=2');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status_code',
                    'data' => [
                        'data' => [
                            '*' => ['id', 'title', 'content', 'user_id', 'created_at', 'updated_at', 'user']
                        ],
                        'current_page',
                        'per_page',
                        'total',
                        'last_page'
                    ],
                    'message'
                ])
                ->assertJsonCount(2, 'data.data')
                ->assertJsonPath('data.current_page', 1)
                ->assertJsonPath('data.per_page', 2);
    }

    public function test_search_returns_empty_when_no_match()
    {
        Post::factory()->create(['user_id' => $this->user->id, 'title' => 'Laravel Tutorial']);

        $response = $this->getJson('/api/posts?search=NonExistent');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status_code',
                    'data' => [
                        'data' => [],
                        'current_page',
                        'per_page',
                        'total'
                    ],
                    'message'
                ])
                ->assertJsonCount(0, 'data.data');
    }

    public function test_can_create_post()
    {
        $postData = [
            'title' => 'Test Post',
            'content' => 'This is a test post content'
        ];

        $response = $this->postJson('/api/posts', $postData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'status_code',
                    'data' => ['id', 'title', 'content', 'user_id', 'created_at', 'updated_at', 'user'],
                    'message'
                ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'content' => 'This is a test post content',
            'user_id' => $this->user->id
        ]);
    }

    public function test_can_show_post()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status_code',
                    'data' => ['id', 'title', 'content', 'user_id', 'created_at', 'updated_at', 'user'],
                    'message'
                ]);
    }

    public function test_can_update_post()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content'
        ];

        $response = $this->patchJson("/api/posts/{$post->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status_code',
                    'data' => ['id', 'title', 'content', 'user_id', 'created_at', 'updated_at', 'user'],
                    'message'
                ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'content' => 'Updated content'
        ]);
    }

    public function test_can_delete_post()
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status_code',
                    'data',
                    'message'
                ]);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }


    public function test_cannot_update_other_users_post()
    {
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $otherUser->id]);

        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content'
        ];

        $response = $this->patchJson("/api/posts/{$post->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_cannot_delete_other_users_post()
    {
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(403);
    }
}
