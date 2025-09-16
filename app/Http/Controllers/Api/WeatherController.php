<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\WeatherHelpers;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
     /**
     * Get current weather data
     *
     * @group Weather
     *
     * Get current weather information for Perth, Australia.
     * This endpoint is public and does not require authentication.
     * Data is cached for 15 minutes to improve performance.
     *
     * Requires OpenWeatherMap API key to be configured in environment variables.
     *
     * @response array{ status_code: int, data: array{ name: string, main: array{ temp: float, humidity: int }, weather: array{ main: string, description: string }[] }, message: string }
     *
     * @response array{ status_code: int, errors: string[], settings: array{} } scenario="service_unavailable"
     */
    public function index()
    {
        $weatherData = WeatherHelpers::getCurrentWeather();

        if (!$weatherData) {
            return response()->serviceUnavailable(['Failed to fetch weather data']);
        }

        return response()->success($weatherData, 'Weather data retrieved successfully');
    }
}
