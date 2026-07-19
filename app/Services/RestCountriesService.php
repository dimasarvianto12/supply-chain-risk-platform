<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RestCountriesService
{
    // Base URL untuk v5
    protected $baseUrl = 'https://api.restcountries.com/countries/v5';

    /**
     * Ambil detail satu negara berdasarkan kode ISO alpha-2 (pakai v5)
     */
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
            if (empty($data) || !is_array($data)) {
                return null;
            }

            $country = $data[0] ?? null;
            if (!$country) {
                return null;
            }

            // Ekstrak mata uang
            $currency = null;
            if (isset($country['currencies'][0]['code'])) {
                $currency = $country['currencies'][0]['code'];
            }

            return [
                'name' => $country['names']['common'] ?? $country['name']['common'] ?? null,
                'capital' => $country['capitals'][0]['name'] ?? $country['capital'][0] ?? null,
                'population' => $country['population'] ?? null,
                'currency' => $currency,
                'flag' => $country['flag']['url_png'] ?? $country['flags']['png'] ?? null,
                'region' => $country['region'] ?? null,
                'latitude' => $country['coordinates']['lat'] ?? $country['latlng'][0] ?? null,
                'longitude' => $country['coordinates']['lng'] ?? $country['latlng'][1] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error("REST Countries error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Ambil SEMUA negara dari REST Countries API v5
     * @return array
     */
    public function getAllCountries(): array
    {
        $apiKey = config('services.rest_countries.api_key');

        if (empty($apiKey)) {
            Log::error("REST Countries API key tidak ditemukan");
            return [];
        }

        $allCountries = [];
        $offset = 0;
        $limit = 100;
        $more = true;

        try {
            while ($more) {
                $url = "{$this->baseUrl}?limit={$limit}&offset={$offset}";
                
                $response = Http::withOptions(['verify' => false, 'timeout' => 60])
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                    ])
                    ->get($url);

                if (!$response->successful()) {
                    Log::warning("REST Countries v5 gagal pada offset {$offset}, status: " . $response->status());
                    break;
                }

                $data = $response->json();
                $objects = $data['data']['objects'] ?? [];
                
                if (empty($objects)) {
                    break;
                }

                foreach ($objects as $country) {
                    $code = $country['codes']['alpha_2'] ?? $country['cca2'] ?? null;
                    if (empty($code)) {
                        continue;
                    }

                    $currency = null;
                    if (!empty($country['currencies'])) {
                        $firstCurrency = reset($country['currencies']);
                        $currency = $firstCurrency['code'] ?? null;
                    }

                    $allCountries[] = [
                        'code' => $code,
                        'name' => $country['names']['common'] ?? $country['name']['common'] ?? null,
                        'capital' => $country['capitals'][0]['name'] ?? $country['capital'][0] ?? null,
                        'population' => $country['population'] ?? 0,
                        'currency' => $currency,
                        'flag' => $country['flag']['url_png'] ?? $country['flags']['png'] ?? null,
                        'region' => $country['region'] ?? null,
                        'latitude' => $country['coordinates']['lat'] ?? $country['latlng'][0] ?? null,
                        'longitude' => $country['coordinates']['lng'] ?? $country['latlng'][1] ?? null,
                    ];
                }

                $meta = $data['data']['meta'] ?? [];
                $more = !empty($meta['more']);
                $offset += $limit;
            }

            return $allCountries;

        } catch (\Exception $e) {
            Log::error("REST Countries v5 error: " . $e->getMessage());
            return $allCountries; // kembalikan yang sudah didapat sejauh ini
        }
    }
}