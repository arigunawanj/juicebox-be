# Weather API Setup Guide

This guide explains how to set up and configure the OpenWeatherMap API integration for the Laravel Quiz API.

## Prerequisites

- Laravel 11 application
- OpenWeatherMap API account
- Valid OpenWeatherMap API key

## Step 1: Get OpenWeatherMap API Key

1. Visit [OpenWeatherMap](https://openweathermap.org/api)
2. Sign up for a free account
3. Navigate to your API keys section
4. Generate a new API key
5. Copy the API key for later use

## Step 2: Configure Environment Variables

Add the following environment variable to your `.env` file:

```env
OPENWEATHER_API_KEY=your_api_key_here
```

Replace `your_api_key_here` with your actual OpenWeatherMap API key.

## Step 3: Update Configuration

The API key is automatically loaded from the environment variable. The configuration is handled in `config/services.php`:

```php
'openweather' => [
    'api_key' => env('OPENWEATHER_API_KEY'),
],
```

## Step 4: Test the Integration

You can test the weather API integration by making a request to:

```
GET /api/weather
```

### Expected Response

```json
{
    "status_code": 200,
    "data": {
        "name": "Jakarta",
        "main": {
            "temp": 30.5,
            "humidity": 75
        },
        "weather": [
            {
                "main": "Clouds",
                "description": "partly cloudy"
            }
        ]
    },
    "message": "Weather data retrieved successfully"
}
```

## Features

- **Caching**: Weather data is cached for 15 minutes to improve performance
- **Error Handling**: Graceful handling of API failures and missing API keys
- **Location**: Currently configured for Jakarta, Indonesia
- **Public Access**: No authentication required

## Troubleshooting

### Common Issues

1. **503 Service Unavailable**: 
   - Check if your API key is valid
   - Verify the API key is correctly set in `.env`
   - Ensure you have sufficient API quota

2. **Missing API Key**:
   - Make sure `OPENWEATHER_API_KEY` is set in your `.env` file
   - Restart your Laravel application after adding the environment variable

3. **API Rate Limits**:
   - Free tier has rate limits
   - Consider upgrading if you need higher limits

### Testing Without API Key

For testing purposes, you can mock the weather API response in your tests:

```php
Http::fake([
    'api.openweathermap.org/*' => Http::response([
        'name' => 'Jakarta',
        'main' => ['temp' => 30.0, 'humidity' => 70],
        'weather' => [['main' => 'Clear', 'description' => 'clear sky']]
    ], 200)
]);
```

## API Documentation

For complete API documentation, visit `/docs/api` when running the application locally.

## Support

If you encounter any issues with the weather API integration, please check:

1. OpenWeatherMap API status
2. Your API key validity
3. Network connectivity
4. Laravel application logs

For more information about OpenWeatherMap API, visit their [documentation](https://openweathermap.org/api).
