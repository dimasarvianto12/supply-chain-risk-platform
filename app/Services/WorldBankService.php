<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorldBankService
{
    protected $baseUrl = 'http://api.worldbank.org/v2/country';

    /**
     * Data fallback untuk negara yang tidak memiliki data di World Bank API
     * (GDP dalam USD, Inflasi dalam %)
     */
    private $fallbackData = [
        'US' => ['gdp' => 27700000000000, 'inflation' => 3.2, 'year' => 2024],
        'GB' => ['gdp' => 3480000000000, 'inflation' => 2.5, 'year' => 2024],
        'ZA' => ['gdp' => 405000000000, 'inflation' => 4.6, 'year' => 2024],
        'BR' => ['gdp' => 2170000000000, 'inflation' => 4.2, 'year' => 2024],
        'IN' => ['gdp' => 3730000000000, 'inflation' => 4.8, 'year' => 2024],
        'JP' => ['gdp' => 4230000000000, 'inflation' => 2.1, 'year' => 2024],
        'DE' => ['gdp' => 4450000000000, 'inflation' => 2.3, 'year' => 2024],
        'CN' => ['gdp' => 17800000000000, 'inflation' => 1.5, 'year' => 2024],
        'ID' => ['gdp' => 1410000000000, 'inflation' => 2.6, 'year' => 2024],
        'AU' => ['gdp' => 1720000000000, 'inflation' => 3.1, 'year' => 2024],
    ];

    /**
     * Ambil GDP (USD) dan Inflasi (%) untuk suatu negara berdasarkan kode ISO.
     * 
     * @param string $countryCode (contoh: 'ID', 'US')
     * @return array|null ['gdp' => ..., 'inflation' => ..., 'year' => ..., 'source' => 'api'|'fallback']
     */
    public function getEconomicData(string $countryCode): ?array
    {
        try {
            // Ambil GDP
            $gdpResult = $this->fetchIndicator($countryCode, 'NY.GDP.MKTP.CD');
            // Ambil Inflasi (annual %)
            $inflationResult = $this->fetchIndicator($countryCode, 'FP.CPI.TOTL.ZG');

            // Jika kedua data null, gunakan fallback
            if ($gdpResult === null && $inflationResult === null) {
                return $this->getFallbackData($countryCode);
            }

            // Data dari API berhasil
            return [
                'gdp' => $gdpResult['value'] ?? null,
                'inflation' => $inflationResult['value'] ?? null,
                'year' => $gdpResult['year'] ?? $inflationResult['year'] ?? date('Y'),
                'source' => 'api',
            ];

        } catch (\Exception $e) {
            Log::error('WorldBank API error: ' . $e->getMessage());
            // Jika error, coba fallback
            return $this->getFallbackData($countryCode);
        }
    }

    /**
     * Fungsi bantu untuk mengambil satu indikator.
     * Kembalikan array ['value' => float, 'year' => int] atau null.
     * 
     * @param string $countryCode
     * @param string $indicator
     * @return array|null
     */
    private function fetchIndicator(string $countryCode, string $indicator): ?array
    {
        $url = "{$this->baseUrl}/{$countryCode}/indicator/{$indicator}?format=json&per_page=1&sort=year:desc";
        
        try {
            $response = Http::timeout(10)->get($url);

            if (!$response->successful()) {
                Log::warning("WorldBank API gagal untuk {$countryCode} - {$indicator}, status: " . $response->status());
                return null;
            }

            $data = $response->json();

            if (!isset($data[1]) || !is_array($data[1]) || empty($data[1])) {
                return null;
            }

            // Ambil data terbaru (index 0) karena sudah di-sort desc
            $latest = $data[1][0] ?? null;
            if (!$latest || !isset($latest['value']) || $latest['value'] === null) {
                return null;
            }

            return [
                'value' => (float) $latest['value'],
                'year' => (int) $latest['date'] ?? date('Y'),
            ];

        } catch (\Exception $e) {
            Log::error("WorldBank API error untuk {$countryCode} - {$indicator}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Ambil data fallback untuk negara tertentu
     */
    private function getFallbackData(string $countryCode): ?array
    {
        if (isset($this->fallbackData[$countryCode])) {
            $data = $this->fallbackData[$countryCode];
            Log::info("Menggunakan data fallback untuk {$countryCode}");
            return [
                'gdp' => $data['gdp'],
                'inflation' => $data['inflation'],
                'year' => $data['year'],
                'source' => 'fallback',
            ];
        }

        Log::warning("Tidak ada data fallback untuk {$countryCode}");
        return null;
    }
}