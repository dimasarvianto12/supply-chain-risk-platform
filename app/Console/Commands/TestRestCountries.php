<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestRestCountries extends Command
{
    protected $signature = 'app:test-rest {code=ID}';
    protected $description = 'Test REST Countries API v5 dengan endpoint yang benar';

    public function handle()
    {
        $code = $this->argument('code');
        $apiKey = config('services.rest_countries.api_key');

        if (empty($apiKey)) {
            $this->error("❌ API key REST Countries tidak ditemukan.");
            return 1;
        }

        $url = "https://api.restcountries.com/countries/v5/codes.alpha_2/{$code}";
        $this->info("Mengambil: {$url}");

        try {
            $response = Http::withOptions(['verify' => false])
                ->withHeaders(['Authorization' => 'Bearer ' . $apiKey])
                ->get($url);

            $this->line("Status: " . $response->status());

            if ($response->successful()) {
                $data = $response->json();
                $objects = $data['data']['objects'] ?? $data['objects'] ?? null;
                
                if (!empty($objects) && isset($objects[0]['names']['common'])) {
                    $country = $objects[0];
                    $this->info("✅ Sukses! Negara: " . $country['names']['common']);
                    $this->line("Populasi: " . ($country['population'] ?? 'N/A'));
                    $capital = $country['capitals'][0]['name'] ?? 'N/A';
                    $this->line("Ibukota: " . $capital);
                    $currencies = $country['currencies'] ?? [];
                    $currencyCodes = array_column($currencies, 'code');
                    $this->line("Mata uang: " . implode(', ', $currencyCodes));
                    $this->line("Region: " . ($country['region'] ?? 'N/A'));
                } else {
                    $this->warn("⚠️ Data tidak ditemukan. Response:");
                    $this->line(json_encode($data, JSON_PRETTY_PRINT));
                }
            } else {
                $this->error("❌ Gagal dengan status " . $response->status());
                $this->line($response->body());
            }

        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
        }

        return 0;
    }
}