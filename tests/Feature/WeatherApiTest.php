<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WeatherApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_get_weather_data()
    {
        // Set a mock API key
        config(['services.openweather.api_key' => 'test_api_key']);

        // Mock the OpenWeatherMap API response
        Http::fake([
            'api.openweathermap.org/*' => Http::response([
                'name' => 'Jakarta',
                'main' => [
                    'temp' => 30.5,
                    'humidity' => 75
                ],
                'weather' => [
                    [
                        'main' => 'Clouds',
                        'description' => 'partly cloudy'
                    ]
                ]
            ], 200)
        ]);

        $response = $this->getJson('/api/weather');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status_code',
                    'data' => [
                        'name',
                        'main' => ['temp', 'humidity'],
                        'weather' => [
                            '*' => ['main', 'description']
                        ]
                    ],
                    'message'
                ])
                ->assertJson([
                    'status_code' => 200,
                    'data' => [
                        'name' => 'Jakarta',
                        'main' => [
                            'temp' => 30.5,
                            'humidity' => 75
                        ]
                    ],
                    'message' => 'Weather data retrieved successfully'
                ]);
    }

    public function test_weather_api_handles_failure_gracefully()
    {
        // Mock API failure
        Http::fake([
            'api.openweathermap.org/*' => Http::response([], 500)
        ]);

        $response = $this->getJson('/api/weather');

        $response->assertStatus(503)
                ->assertJsonStructure([
                    'status_code',
                    'errors',
                    'settings'
                ]);
    }

    public function test_weather_api_handles_missing_api_key()
    {
        // Set empty API key
        config(['services.openweather.api_key' => null]);

        $response = $this->getJson('/api/weather');

        $response->assertStatus(503)
                ->assertJsonStructure([
                    'status_code',
                    'errors',
                    'settings'
                ]);
    }
}
