<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestExchangeRate extends Command
{
    protected $signature = 'app:test-rate {base=USD} {target=IDR}';
    protected $description = 'Test ExchangeRate API dengan API Key';

    public function handle()
    {
        $base = $this->argument('base');
        $target = $this->argument('target');
        $apiKey = config('services.exchange_rate.api_key');

        if (empty($apiKey)) {
            $this->error("❌ API key ExchangeRate tidak ditemukan. Pastikan EXCHANGE_RATE_API_KEY di .env");
            return 1;
        }

        $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/pair/{$base}/{$target}";
        $this->info("Mengambil: {$url}");

        try {
            $response = Http::withOptions(['verify' => false])->get($url);

            $this->line("Status: " . $response->status());

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['conversion_rate'])) {
                    $this->info("✅ Kurs: 1 {$base} = {$data['conversion_rate']} {$target}");
                } else {
                    $this->warn("⚠️ Tidak ada conversion_rate dalam response.");
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