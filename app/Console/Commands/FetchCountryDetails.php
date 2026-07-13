<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Services\RestCountriesService;

class FetchCountryDetails extends Command
{
    protected $signature = 'app:fetch-countries {country? : Kode negara opsional}';
    protected $description = 'Ambil detail negara dari REST Countries API (populasi, mata uang, bendera, dll)';

    protected $restService;

    public function __construct(RestCountriesService $restService)
    {
        parent::__construct();
        $this->restService = $restService;
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
            $this->info("\nMemproses {$country->code}...");

            $details = $this->restService->getCountryDetails($country->code);

            if ($details) {
                $country->update([
                    'name' => $details['name'] ?? $country->name,
                    'capital' => $details['capital'],
                    'population' => $details['population'] ?? $country->population,
                    'currency' => $details['currency'] ?? $country->currency,
                    'flag' => $details['flag'],
                    'region' => $details['region'],
                    'latitude' => $details['latitude'] ?? $country->latitude,
                    'longitude' => $details['longitude'] ?? $country->longitude,
                ]);

                $this->info("  ✅ Populasi: {$details['population']}, Mata uang: {$details['currency']}");
            } else {
                $this->warn("  ⚠️ Gagal mengambil detail.");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✅ Selesai memperbarui data negara.');
        return 0;
    }
}