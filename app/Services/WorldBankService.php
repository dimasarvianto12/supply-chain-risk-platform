<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorldBankService
{
    protected $baseUrl = 'http://api.worldbank.org/v2/country';

    /**
     * Ambil GDP (USD) dan Inflasi (%) untuk suatu negara berdasarkan kode ISO.
     * 
     * @param string $countryCode (contoh: 'ID', 'US')
     * @return array|null ['gdp' => ..., 'inflation' => ..., 'year' => ...]
     */
    public function getEconomicData(string $countryCode): ?array
    {
        try {
            // Ambil GDP
            $gdp = $this->fetchIndicator($countryCode, 'NY.GDP.MKTP.CD');
            // Ambil Inflasi (annual %)
            $inflation = $this->fetchIndicator($countryCode, 'FP.CPI.TOTL.ZG');

            if ($gdp === null && $inflation === null) {
                return null;
            }

            return [
                'gdp' => $gdp,
                'inflation' => $inflation,
                'year' => date('Y'), // atau ambil dari data jika ada
            ];

        } catch (\Exception $e) {
            Log::error('WorldBank API error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fungsi bantu untuk mengambil satu indikator.
     * 
     * @param string $countryCode
     * @param string $indicator
     * @return float|null
     */
    private function fetchIndicator(string $countryCode, string $indicator): ?float
    {
        $url = "{$this->baseUrl}/{$countryCode}/indicator/{$indicator}?format=json&per_page=1";
        $response = Http::get($url);

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();

        // Struktur World Bank: [ metadata, [ {...data...} ] ]
        if (!isset($data[1]) || !is_array($data[1]) || empty($data[1])) {
            return null;
        }

        // Ambil data terbaru (index 0)
        $latest = $data[1][0] ?? null;
        if (!$latest || !isset($latest['value']) || $latest['value'] === null) {
            return null;
        }

        return (float) $latest['value'];
    }
}