<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UnauthenticatedApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_access_posts_without_authentication()
    {
        $response = $this->getJson('/api/posts');
        $response->assertStatus(401);
    }

    public function test_cannot_create_post_without_authentication()
    {
        $postData = [
            'title' => 'Test Post',
            'content' => 'This is a test post content'
        ];

        $response = $this->postJson('/api/posts', $postData);
        $response->assertStatus(401);
    }

    public function test_cannot_access_user_without_authentication()
    {
        $user = User::factory()->create();
        $response = $this->getJson("/api/users/{$user->id}");
        $response->assertStatus(401);
    }

    public function test_cannot_access_current_user_without_authentication()
    {
        $response = $this->getJson('/api/user');
        $response->assertStatus(401);
    }

    public function test_can_access_weather_without_authentication()
    {
        // Mock the OpenWeatherMap API response
        Http::fake([
            'api.openweathermap.org/*' => Http::response([
                'name' => 'Jakarta',
                'main' => ['temp' => 30.0, 'humidity' => 70],
                'weather' => [['main' => 'Clear', 'description' => 'clear sky']]
            ], 200)
        ]);

        $response = $this->getJson('/api/weather');
        $response->assertStatus(200);
    }
}
