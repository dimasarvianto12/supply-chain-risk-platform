<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RestCountriesService
{
    protected $baseUrl = 'https://restcountries.com/v5';

    public function getCountryDetails(string $countryCode): ?array
    {
        $apiKey = config('services.rest_countries.api_key');

        if (empty($apiKey)) {
            Log::error("REST Countries API key tidak ditemukan");
            return null;
        }

        try {
            $url = "{$this->baseUrl}/alpha/{$countryCode}";
            
            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                ])
                ->get($url);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            // Log untuk debugging
            Log::info('REST Countries response', ['data' => json_encode($data, JSON_PRETTY_PRINT)]);

            // Coba berbagai kemungkinan struktur response
            $country = null;

            // Struktur 1: langsung array of objects
            if (isset($data[0]) && is_array($data[0])) {
                $country = $data[0];
            }
            // Struktur 2: data.objects[0]
            elseif (isset($data['data']['objects'][0]) && is_array($data['data']['objects'][0])) {
                $country = $data['data']['objects'][0];
            }
            // Struktur 3: objects[0]
            elseif (isset($data['objects'][0]) && is_array($data['objects'][0])) {
                $country = $data['objects'][0];
            }

            if (!$country) {
                Log::warning("Tidak dapat menemukan data negara untuk {$countryCode}");
                return null;
            }

            // Ekstrak data dengan berbagai kemungkinan nama field
            return [
                'name' => $country['name']['common'] ?? $country['names']['common'] ?? $country['name'] ?? null,
                'capital' => $this->extractCapital($country),
                'population' => $country['population'] ?? null,
                'currency' => $this->extractCurrencyCode($country),
                'flag' => $this->extractFlag($country),
                'region' => $country['region'] ?? $country['region']['name'] ?? null,
                'latitude' => $this->extractLatitude($country),
                'longitude' => $this->extractLongitude($country),
            ];

        } catch (\Exception $e) {
            Log::error("REST Countries error: " . $e->getMessage());
            return null;
        }
    }

    private function extractCapital(array $country): ?string
    {
        // Coba berbagai format capital
        if (isset($country['capital'][0]['name'])) {
            return $country['capital'][0]['name'];
        }
        if (isset($country['capital'][0])) {
            return $country['capital'][0];
        }
        if (isset($country['capital'])) {
            return is_array($country['capital']) ? $country['capital'][0] : $country['capital'];
        }
        return null;
    }

    private function extractCurrencyCode(array $country): ?string
    {
        $currencies = $country['currencies'] ?? [];
        if (empty($currencies)) {
            return null;
        }
        // Coba berbagai format currencies
        if (isset($currencies[0]['code'])) {
            return $currencies[0]['code'];
        }
        if (isset($currencies[0])) {
            return $currencies[0];
        }
        // Jika currencies adalah object dengan key sebagai kode mata uang
        if (is_array($currencies) && !isset($currencies[0])) {
            $keys = array_keys($currencies);
            return $keys[0] ?? null;
        }
        return null;
    }

    private function extractFlag(array $country): ?string
    {
        $flag = $country['flag'] ?? null;
        if (is_array($flag)) {
            return $flag['url_png'] ?? $flag['url_svg'] ?? $flag['png'] ?? $flag['svg'] ?? null;
        }
        if (is_string($flag)) {
            return $flag;
        }
        // Coba flag di field lain
        return $country['flags']['png'] ?? $country['flags']['svg'] ?? null;
    }

    private function extractLatitude(array $country): ?float
    {
        $coords = $country['coordinates'] ?? $country['latlng'] ?? null;
        if (is_array($coords)) {
            return $coords['lat'] ?? $coords[0] ?? null;
        }
        return null;
    }

    private function extractLongitude(array $country): ?float
    {
        $coords = $country['coordinates'] ?? $country['latlng'] ?? null;
        if (is_array($coords)) {
            return $coords['lng'] ?? $coords[1] ?? null;
        }
        return null;
    }
}