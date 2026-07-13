<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RestCountriesService
{
    protected $baseUrl = 'https://api.restcountries.com/countries/v5';

    public function getCountryDetails(string $countryCode): ?array
    {
        $apiKey = config('services.rest_countries.api_key');

        if (empty($apiKey)) {
            Log::error("REST Countries API key tidak ditemukan");
            return null;
        }

        try {
            $url = "{$this->baseUrl}/codes.alpha_2/{$countryCode}";
            
            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                ])
                ->get($url);

            if (!$response->successful()) {
                Log::warning("REST Countries gagal untuk {$countryCode}, status: " . $response->status());
                return null;
            }

            $data = $response->json();

            // Struktur response v5: data.objects
            $objects = $data['data']['objects'] ?? $data['objects'] ?? null;
            if (empty($objects) || !is_array($objects)) {
                return null;
            }

            $country = $objects[0] ?? null;
            if (!$country) {
                return null;
            }

            return [
                'name' => $country['names']['common'] ?? $country['names']['official'] ?? null,
                'capital' => $country['capitals'][0]['name'] ?? null,
                'population' => $country['population'] ?? null,
                'currency' => $this->extractCurrencyCode($country['currencies'] ?? []),
                'flag' => $country['flag']['url_png'] ?? $country['flag']['url_svg'] ?? null,
                'region' => $country['region'] ?? null,
                'latitude' => $country['capitals'][0]['coordinates']['lat'] ?? null,
                'longitude' => $country['capitals'][0]['coordinates']['lng'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error("REST Countries error: " . $e->getMessage());
            return null;
        }
    }

    private function extractCurrencyCode(array $currencies): ?string
    {
        if (empty($currencies)) {
            return null;
        }
        return $currencies[0]['code'] ?? null;
    }
}