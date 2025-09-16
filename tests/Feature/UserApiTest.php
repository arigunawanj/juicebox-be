<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_show_user()
    {
        $response = $this->getJson("/api/users/{$this->user->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status_code',
                    'data' => ['id', 'name', 'email', 'created_at', 'updated_at', 'posts'],
                    'message'
                ]);
    }

    public function test_cannot_show_nonexistent_user()
    {
        $response = $this->getJson('/api/users/999');

        $response->assertStatus(404)
                ->assertJsonStructure([
                    'status_code',
                    'errors',
                    'settings'
                ]);
    }

    public function test_can_get_current_user()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status_code',
                    'data' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                    'message'
                ]);
    }

}
