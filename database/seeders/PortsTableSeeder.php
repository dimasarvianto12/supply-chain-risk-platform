<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Port;

class PortsTableSeeder extends Seeder
{
    public function run(): void
    {
        $ports = [
            // Indonesia
            ['name' => 'Tanjung Priok', 'country' => 'Indonesia', 'latitude' => -6.1048, 'longitude' => 106.8957, 'status' => 'active'],
            ['name' => 'Tanjung Perak', 'country' => 'Indonesia', 'latitude' => -7.2129, 'longitude' => 112.7311, 'status' => 'active'],
            ['name' => 'Belawan', 'country' => 'Indonesia', 'latitude' => 3.7834, 'longitude' => 98.6833, 'status' => 'active'],
            
            // China
            ['name' => 'Shanghai Port', 'country' => 'China', 'latitude' => 31.2304, 'longitude' => 121.4737, 'status' => 'active'],
            ['name' => 'Ningbo-Zhoushan', 'country' => 'China', 'latitude' => 29.8683, 'longitude' => 121.5440, 'status' => 'active'],
            ['name' => 'Shenzhen Port', 'country' => 'China', 'latitude' => 22.5431, 'longitude' => 114.0579, 'status' => 'active'],
            ['name' => 'Guangzhou Port', 'country' => 'China', 'latitude' => 23.1291, 'longitude' => 113.2644, 'status' => 'active'],
            
            // Singapore
            ['name' => 'Port of Singapore', 'country' => 'Singapore', 'latitude' => 1.2640, 'longitude' => 103.8190, 'status' => 'active'],
            
            // Germany
            ['name' => 'Port of Hamburg', 'country' => 'Germany', 'latitude' => 53.5511, 'longitude' => 9.9937, 'status' => 'active'],
            ['name' => 'Port of Bremerhaven', 'country' => 'Germany', 'latitude' => 53.5511, 'longitude' => 8.5767, 'status' => 'active'],
            
            // Netherlands
            ['name' => 'Port of Rotterdam', 'country' => 'Netherlands', 'latitude' => 51.9225, 'longitude' => 4.4792, 'status' => 'active'],
            
            // United States
            ['name' => 'Port of Los Angeles', 'country' => 'United States', 'latitude' => 33.7380, 'longitude' => -118.2378, 'status' => 'active'],
            ['name' => 'Port of Long Beach', 'country' => 'United States', 'latitude' => 33.7540, 'longitude' => -118.1840, 'status' => 'active'],
            ['name' => 'Port of New York', 'country' => 'United States', 'latitude' => 40.7128, 'longitude' => -74.0060, 'status' => 'active'],
            
            // United Kingdom
            ['name' => 'Port of London', 'country' => 'United Kingdom', 'latitude' => 51.5074, 'longitude' => -0.1278, 'status' => 'active'],
            ['name' => 'Port of Southampton', 'country' => 'United Kingdom', 'latitude' => 50.9097, 'longitude' => -1.4044, 'status' => 'active'],
            
            // Japan
            ['name' => 'Port of Tokyo', 'country' => 'Japan', 'latitude' => 35.6762, 'longitude' => 139.6503, 'status' => 'active'],
            ['name' => 'Port of Yokohama', 'country' => 'Japan', 'latitude' => 35.4437, 'longitude' => 139.6380, 'status' => 'active'],
            
            // India
            ['name' => 'Port of Mumbai', 'country' => 'India', 'latitude' => 18.9220, 'longitude' => 72.8347, 'status' => 'active'],
            ['name' => 'Port of Chennai', 'country' => 'India', 'latitude' => 13.0827, 'longitude' => 80.2707, 'status' => 'active'],
            
            // Australia
            ['name' => 'Port of Sydney', 'country' => 'Australia', 'latitude' => -33.8688, 'longitude' => 151.2093, 'status' => 'active'],
            ['name' => 'Port of Melbourne', 'country' => 'Australia', 'latitude' => -37.8136, 'longitude' => 144.9631, 'status' => 'active'],
            
            // Brazil
            ['name' => 'Port of Santos', 'country' => 'Brazil', 'latitude' => -23.9608, 'longitude' => -46.3322, 'status' => 'active'],
            
            // South Africa
            ['name' => 'Port of Durban', 'country' => 'South Africa', 'latitude' => -29.8587, 'longitude' => 31.0218, 'status' => 'active'],
            ['name' => 'Port of Cape Town', 'country' => 'South Africa', 'latitude' => -33.9249, 'longitude' => 18.4241, 'status' => 'active'],
            
            // France
            ['name' => 'Port of Marseille', 'country' => 'France', 'latitude' => 43.2965, 'longitude' => 5.3698, 'status' => 'active'],
            ['name' => 'Port of Le Havre', 'country' => 'France', 'latitude' => 49.4944, 'longitude' => 0.1079, 'status' => 'active'],
            
            // Canada
            ['name' => 'Port of Vancouver', 'country' => 'Canada', 'latitude' => 49.2827, 'longitude' => -123.1207, 'status' => 'active'],
            ['name' => 'Port of Montreal', 'country' => 'Canada', 'latitude' => 45.5017, 'longitude' => -73.5673, 'status' => 'active'],
            
            // Italy
            ['name' => 'Port of Genoa', 'country' => 'Italy', 'latitude' => 44.4056, 'longitude' => 8.9463, 'status' => 'active'],
            ['name' => 'Port of Naples', 'country' => 'Italy', 'latitude' => 40.8518, 'longitude' => 14.2681, 'status' => 'active'],
            
            // South Korea
            ['name' => 'Port of Busan', 'country' => 'South Korea', 'latitude' => 35.1017, 'longitude' => 129.0300, 'status' => 'active'],
            ['name' => 'Port of Incheon', 'country' => 'South Korea', 'latitude' => 37.4563, 'longitude' => 126.7052, 'status' => 'active'],
            
            // Mexico
            ['name' => 'Port of Veracruz', 'country' => 'Mexico', 'latitude' => 19.2003, 'longitude' => -96.1328, 'status' => 'active'],
            ['name' => 'Port of Manzanillo', 'country' => 'Mexico', 'latitude' => 19.0664, 'longitude' => -104.3160, 'status' => 'active'],
        ];

        $levels = ['low', 'medium', 'high'];

        foreach ($ports as $port) {
            $congestion = $levels[array_rand($levels)];
            $delay = 0;
            if ($congestion === 'medium') {
                $delay = rand(1, 3);
            } elseif ($congestion === 'high') {
                $delay = rand(4, 10);
            }

            $port['congestion_level'] = $congestion;
            $port['delay_days'] = $delay;

            Port::updateOrCreate(
                ['name' => $port['name']],
                $port
            );
        }

        // Generate generic ports for ALL countries that don't have one yet
        $allCountries = \App\Models\Country::all();
        foreach ($allCountries as $countryModel) {
            $existingPort = Port::where('country', $countryModel->name)->first();
            
            if (!$existingPort) {
                $congestion = $levels[array_rand($levels)];
                $delay = 0;
                if ($congestion === 'medium') {
                    $delay = rand(1, 3);
                } elseif ($congestion === 'high') {
                    $delay = rand(4, 10);
                }

                Port::create([
                    'name' => 'Port of ' . $countryModel->name,
                    'country' => $countryModel->name,
                    'latitude' => $countryModel->latitude ?? 0,
                    'longitude' => $countryModel->longitude ?? 0,
                    'status' => 'active',
                    'congestion_level' => $congestion,
                    'delay_days' => $delay,
                ]);
            }
        }

        $this->command->info('✅ Data pelabuhan berhasil di-seed (Total: ' . Port::count() . ')!');
    }
}