<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    protected $baseUrl = 'https://v6.exchangerate-api.com/v6';

        public function getRate(string $base, string $target): ?float
    {
        $apiKey = config('services.exchange_rate.api_key');
        if (empty($apiKey)) {
            Log::error("ExchangeRate API key tidak ditemukan");
            return null;
        }

        try {
            $url = "{$this->baseUrl}/{$apiKey}/pair/{$base}/{$target}";
            $response = Http::withOptions(['verify' => false])->get($url);

            if (!$response->successful()) {
                Log::warning("ExchangeRate API gagal, status: " . $response->status());
                return null;
            }

            $data = $response->json();
            return $data['conversion_rate'] ?? null;
        } catch (\Exception $e) {
            Log::error("ExchangeRate API error: " . $e->getMessage());
            return null;
        }
    }
}