<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\WeatherCache;
use App\Services\OpenMeteoService;

class FetchWeatherData extends Command
{
    protected $signature = 'app:fetch-weather {country? : Kode negara (opsional, jika kosong ambil semua)}';
    protected $description = 'Ambil data cuaca dari Open-Meteo untuk semua negara atau satu negara tertentu';

    protected $weatherService;

    public function __construct(OpenMeteoService $weatherService)
    {
        parent::__construct();
        $this->weatherService = $weatherService;
    }

    public function handle()
    {
        $countryCode = $this->argument('country');

        if ($countryCode) {
            $countries = Country::where('code', $countryCode)->get();
        } else {
            $countries = Country::all();
        }

        if ($countries->isEmpty()) {
            $this->error('Negara tidak ditemukan.');
            return 1;
        }

        $bar = $this->output->createProgressBar($countries->count());
        $bar->start();

        foreach ($countries as $country) {
            $this->info("\nMemproses {$country->name} ({$country->code})...");

            if (!$country->latitude || !$country->longitude) {
                $this->warn("  Skipping: koordinat tidak tersedia.");
                $bar->advance();
                continue;
            }

            $weather = $this->weatherService->getCurrentWeather(
                $country->latitude,
                $country->longitude
            );

            if ($weather) {
                WeatherCache::create([
                    'country_id' => $country->id,
                    'temperature' => $weather['temperature'],
                    'humidity' => $weather['humidity'],
                    'wind_speed' => $weather['wind_speed'],
                    'weather_code' => $weather['weather_code'],
                    'weather_description' => OpenMeteoService::getWeatherDescription($weather['weather_code']),
                    'recorded_at' => $weather['recorded_at'],
                ]);

                $this->info("  ✅ Cuaca berhasil disimpan.");
            } else {
                $this->warn("  ⚠️ Gagal mengambil data cuaca.");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✅ Selesai memperbarui data cuaca.');
        return 0;
    }
}