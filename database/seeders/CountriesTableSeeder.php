<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Services\RestCountriesService;

class CountriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Fetching countries from REST Countries API...');
        
        $service = new RestCountriesService();
        $countries = $service->getAllCountries();

        if (empty($countries)) {
            $this->command->error('Failed to fetch countries from API. Please check your API key and connection.');
            return;
        }

        $this->command->info('Processing ' . count($countries) . ' countries...');

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                [
                    'name' => $country['name'],
                    'capital' => $country['capital'] ?? null,
                    'population' => $country['population'] ?? 0,
                    'currency' => $country['currency'] ?? null,
                    'flag' => $country['flag'] ?? null,
                    'region' => $country['region'] ?? null,
                    'latitude' => $country['latitude'] ?? null,
                    'longitude' => $country['longitude'] ?? null,
                ]
            );
        }

        $this->command->info('✅ Successfully seeded ' . count($countries) . ' countries!');
    }
}