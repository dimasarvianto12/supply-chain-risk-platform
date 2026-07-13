<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['code' => 'DE', 'name' => 'Germany', 'capital' => 'Berlin', 'population' => 83000000, 'currency' => 'EUR', 'latitude' => 51.1657, 'longitude' => 10.4515],
            ['code' => 'CN', 'name' => 'China', 'capital' => 'Beijing', 'population' => 1412000000, 'currency' => 'CNY', 'latitude' => 35.8617, 'longitude' => 104.1954],
            ['code' => 'ID', 'name' => 'Indonesia', 'capital' => 'Jakarta', 'population' => 273500000, 'currency' => 'IDR', 'latitude' => -0.7893, 'longitude' => 113.9213],
            ['code' => 'AU', 'name' => 'Australia', 'capital' => 'Canberra', 'population' => 25690000, 'currency' => 'AUD', 'latitude' => -25.2744, 'longitude' => 133.7751],
            ['code' => 'US', 'name' => 'United States', 'capital' => 'Washington D.C.', 'population' => 331900000, 'currency' => 'USD', 'latitude' => 37.0902, 'longitude' => -95.7129],
            ['code' => 'GB', 'name' => 'United Kingdom', 'capital' => 'London', 'population' => 67220000, 'currency' => 'GBP', 'latitude' => 55.3781, 'longitude' => -3.4360],
            ['code' => 'JP', 'name' => 'Japan', 'capital' => 'Tokyo', 'population' => 125800000, 'currency' => 'JPY', 'latitude' => 36.2048, 'longitude' => 138.2529],
            ['code' => 'IN', 'name' => 'India', 'capital' => 'New Delhi', 'population' => 1380000000, 'currency' => 'INR', 'latitude' => 20.5937, 'longitude' => 78.9629],
            ['code' => 'BR', 'name' => 'Brazil', 'capital' => 'Brasília', 'population' => 213000000, 'currency' => 'BRL', 'latitude' => -14.2350, 'longitude' => -51.9253],
            ['code' => 'ZA', 'name' => 'South Africa', 'capital' => 'Pretoria', 'population' => 59310000, 'currency' => 'ZAR', 'latitude' => -30.5595, 'longitude' => 22.9375],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(['code' => $country['code']], $country);
        }
    }
}