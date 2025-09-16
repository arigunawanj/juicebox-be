<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_register_user()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'status_code',
                    'data' => ['user' => ['id', 'name', 'email', 'created_at', 'updated_at'], 'token'],
                    'message'
                ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
    }

    public function test_cannot_register_with_invalid_data()
    {
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'status_code',
                    'errors',
                    'settings'
                ]);
    }

    public function test_can_login_user()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status_code',
                    'data' => ['user' => ['id', 'name', 'email', 'created_at', 'updated_at'], 'token'],
                    'message'
                ]);
    }

    public function test_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(401)
                ->assertJsonStructure([
                    'status_code',
                    'errors',
                    'settings'
                ]);
    }

    public function test_can_logout_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status_code',
                    'data',
                    'message'
                ]);
    }

    public function test_can_get_current_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status_code',
                    'data' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                    'message'
                ]);
    }

    public function test_cannot_access_protected_routes_without_authentication()
    {
        $response = $this->postJson('/api/logout');
        $response->assertStatus(401);

        $response = $this->getJson('/api/user');
        $response->assertStatus(401);
    }
}
