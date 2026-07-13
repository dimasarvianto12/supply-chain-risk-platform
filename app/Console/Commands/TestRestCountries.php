<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestRestCountries extends Command
{
    protected $signature = 'app:test-rest {code=ID}';
    protected $description = 'Test REST Countries API v5';

    public function handle()
    {
        $code = $this->argument('code');
        $apiKey = config('services.rest_countries.api_key');

        if (empty($apiKey)) {
            $this->error("❌ API key tidak ditemukan.");
            return 1;
        }

        $url = "https://restcountries.com/v5/alpha/{$code}";
        $this->info("Mengambil: {$url}");

        try {
            $response = Http::withOptions(['verify' => false])
                ->withHeaders(['Authorization' => 'Bearer ' . $apiKey])
                ->get($url);

            $this->line("Status: " . $response->status());

            if ($response->successful()) {
                $data = $response->json();
                
                // Tampilkan struktur data untuk debugging
                $this->line("\n📦 Struktur Response:");
                $this->line(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $this->newLine();

                // Coba ekstrak nama negara
                $country = null;
                if (isset($data[0])) {
                    $country = $data[0];
                } elseif (isset($data['data']['objects'][0])) {
                    $country = $data['data']['objects'][0];
                } elseif (isset($data['objects'][0])) {
                    $country = $data['objects'][0];
                }

                if ($country) {
                    $name = $country['name']['common'] ?? $country['names']['common'] ?? $country['name'] ?? 'Tidak diketahui';
                    $this->info("✅ Sukses! Negara: " . $name);
                    
                    // Tampilkan beberapa data
                    $capital = $this->extractCapital($country);
                    if ($capital) $this->line("Ibukota: " . $capital);
                    if (isset($country['population'])) $this->line("Populasi: " . number_format($country['population']));
                    
                    $currency = $this->extractCurrency($country);
                    if ($currency) $this->line("Mata uang: " . $currency);
                } else {
                    $this->warn("⚠️ Tidak dapat menemukan data negara.");
                }
            } else {
                $this->error("❌ Gagal: " . $response->body());
            }

        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
        }

        return 0;
    }

    private function extractCapital(array $country): ?string
    {
        if (isset($country['capital'][0]['name'])) return $country['capital'][0]['name'];
        if (isset($country['capital'][0])) return $country['capital'][0];
        if (isset($country['capital'])) return is_array($country['capital']) ? $country['capital'][0] : $country['capital'];
        return null;
    }

    private function extractCurrency(array $country): ?string
    {
        $currencies = $country['currencies'] ?? [];
        if (empty($currencies)) return null;
        
        if (isset($currencies[0]['code'])) return $currencies[0]['code'];
        if (isset($currencies[0])) return $currencies[0];
        
        $keys = array_keys($currencies);
        return $keys[0] ?? null;
    }
}