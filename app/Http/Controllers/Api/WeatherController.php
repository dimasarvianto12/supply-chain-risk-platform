<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    /**
     * GET /api/weather
     * Mengembalikan data cuaca terbaru untuk semua negara yang memiliki koordinat
     */
    public function index()
    {
        $countries = Country::with('latestWeather')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $result = $countries->map(function ($country) {
            $weather = $country->latestWeather;
            return [
                'code' => $country->code,
                'name' => $country->name,
                'latitude' => (float) $country->latitude,
                'longitude' => (float) $country->longitude,
                'weather' => $weather ? [
                    'temperature' => $weather->temperature,
                    'humidity' => $weather->humidity,
                    'wind_speed' => $weather->wind_speed,
                    'weather_code' => $weather->weather_code,
                    'description' => $weather->weather_description,
                ] : null,
            ];
        });

        return response()->json($result);
    }
}