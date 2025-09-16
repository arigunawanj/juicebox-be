<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherHelpers
{
    /**
     * Get current weather data
     */
    public static function getCurrentWeather()
    {
        try {
            // Check cache first (15 minutes)
            $cacheKey = 'weather_perth';
            $weatherData = Cache::get($cacheKey);

            if (!$weatherData) {
                // Fetch from OpenWeatherMap API
                $apiKey = config('services.openweather.api_key');
                $city = 'Perth,AU';

                if (!$apiKey) {
                    return null; // Return null if API key not configured
                }

                $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                    'q' => $city,
                    'appid' => $apiKey,
                    'units' => 'metric'
                ]);

                if (!$response->successful()) {
                    Log::error('Weather API failed', ['status' => $response->status(), 'body' => $response->body()]);
                    return null; // Return null if API fails
                }

                $weatherData = $response->json();

                // Cache for 15 minutes
                Cache::put($cacheKey, $weatherData, now()->addMinutes(15));
            }

            return $weatherData;

        } catch (\Exception $e) {
            Log::error('Weather API error', ['error' => $e->getMessage()]);
            return null; // Return null on exception
        }
    }

}
