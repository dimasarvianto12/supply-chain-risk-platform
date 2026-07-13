<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenMeteoService
{
    protected $baseUrl = 'https://api.open-meteo.com/v1/forecast';

    /**
     * Ambil data cuaca terkini untuk suatu koordinat.
     * 
     * @param float $lat
     * @param float $lon
     * @return array|null
     */
    public function getCurrentWeather(float $lat, float $lon): ?array
    {
        try {
            $response = Http::get($this->baseUrl, [
                'latitude' => $lat,
                'longitude' => $lon,
                'current_weather' => 'true',
                'hourly' => 'temperature_2m,relative_humidity_2m,wind_speed_10m',
                'timezone' => 'auto',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Ambil data current_weather
                $current = $data['current_weather'] ?? null;
                if (!$current) return null;

                // Ambil data hourly untuk humidity (karena current_weather tidak menyertakan humidity)
                $hourly = $data['hourly'] ?? null;
                $humidity = null;
                if ($hourly && isset($hourly['relative_humidity_2m'][0])) {
                    $humidity = $hourly['relative_humidity_2m'][0];
                }

                return [
                    'temperature' => $current['temperature'] ?? null,
                    'wind_speed' => $current['windspeed'] ?? null,
                    'weather_code' => $current['weathercode'] ?? null,
                    'humidity' => $humidity,
                    'recorded_at' => $current['time'] ?? now(),
                ];
            }

            Log::warning('Open-Meteo API gagal', ['lat' => $lat, 'lon' => $lon]);
            return null;

        } catch (\Exception $e) {
            Log::error('Open-Meteo API error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Konversi kode cuaca Open-Meteo menjadi deskripsi singkat.
     * 
     * @param int|null $code
     * @return string
     */
    public static function getWeatherDescription(?int $code): string
    {
        if ($code === null) return 'Tidak diketahui';

        $map = [
            0 => 'Cerah',
            1 => 'Cerah berawan',
            2 => 'Berawan sebagian',
            3 => 'Mendung',
            45 => 'Kabut',
            48 => 'Kabut beku',
            51 => 'Gerimis ringan',
            53 => 'Gerimis sedang',
            55 => 'Gerimis lebat',
            56 => 'Gerimis beku ringan',
            57 => 'Gerimis beku lebat',
            61 => 'Hujan ringan',
            63 => 'Hujan sedang',
            65 => 'Hujan lebat',
            66 => 'Hujan beku ringan',
            67 => 'Hujan beku lebat',
            71 => 'Salju ringan',
            73 => 'Salju sedang',
            75 => 'Salju lebat',
            77 => 'Butiran salju',
            80 => 'Hujan deras ringan',
            81 => 'Hujan deras sedang',
            82 => 'Hujan deras lebat',
            85 => 'Hujan salju ringan',
            86 => 'Hujan salju lebat',
            95 => 'Badai petir ringan',
            96 => 'Badai petir dengan hujan es ringan',
            99 => 'Badai petir dengan hujan es lebat',
        ];

        return $map[$code] ?? 'Cuaca khusus';
    }
}