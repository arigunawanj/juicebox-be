<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateWeatherJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $apiKey = config('services.openweather.api_key');
            $city = 'Jakarta,ID';

            if (!$apiKey) {
                Log::error('Weather API key not configured for background job');
                return;
            }

            $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric'
            ]);

            if ($response->successful()) {
                $weatherData = $response->json();
                Cache::put('weather_jakarta', $weatherData, now()->addMinutes(15));
                Log::info('Weather data updated successfully via background job');
            } else {
                Log::error('Weather API failed in background job', ['status' => $response->status()]);
            }

        } catch (\Exception $e) {
            Log::error('Weather background job error', ['error' => $e->getMessage()]);
        }
    }
}
