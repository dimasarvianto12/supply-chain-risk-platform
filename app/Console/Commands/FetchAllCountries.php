<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Services\RestCountriesService;

class FetchAllCountries extends Command
{
    protected $signature = 'app:fetch-all-countries';
    protected $description = 'Ambil semua negara dari REST Countries API dan simpan ke database';

    protected $restService;

    public function __construct(RestCountriesService $restService)
    {
        parent::__construct();
        $this->restService = $restService;
    }

    public function handle()
    {
        $this->info('📡 Mengambil data semua negara dari REST Countries API...');

        try {
            $countries = $this->restService->getAllCountries();
        } catch (\Exception $e) {
            $this->error('❌ Error saat memanggil service: ' . $e->getMessage());
            return 1;
        }

        if (empty($countries)) {
            $this->error('❌ Gagal mengambil data negara. Periksa API key dan koneksi internet.');
            $this->warn('Cek log di storage/logs/laravel.log untuk detail.');
            return 1;
        }

        $this->info('📦 Mendapatkan ' . count($countries) . ' negara dari API.');

        $bar = $this->output->createProgressBar(count($countries));
        $bar->start();

        $saved = 0;
        foreach ($countries as $data) {
            if (empty($data['code']) || empty($data['name'])) {
                $bar->advance();
                continue;
            }

            Country::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'capital' => $data['capital'],
                    'population' => $data['population'],
                    'currency' => $data['currency'],
                    'flag' => $data['flag'],
                    'region' => $data['region'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                ]
            );
            $saved++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Berhasil menyimpan {$saved} negara ke database.");
        return 0;
    }
}