<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RiskCalculator;

class UpdateRiskScores extends Command
{
    protected $signature = 'app:update-risk {country? : Kode negara (opsional)}';
    protected $description = 'Hitung dan simpan skor risiko untuk semua negara atau satu negara tertentu';

    protected $riskCalculator;

    public function __construct(RiskCalculator $riskCalculator)
    {
        parent::__construct();
        $this->riskCalculator = $riskCalculator;
    }

    public function handle()
    {
        $countryCode = $this->argument('country');

        if ($countryCode) {
            $country = \App\Models\Country::where('code', $countryCode)->first();
            if (!$country) {
                $this->error('Negara tidak ditemukan.');
                return 1;
            }
            $countries = collect([$country]);
        } else {
            $countries = \App\Models\Country::all();
        }

        if ($countries->isEmpty()) {
            $this->error('Tidak ada negara.');
            return 1;
        }

        $bar = $this->output->createProgressBar($countries->count());
        $bar->start();

        foreach ($countries as $country) {
            $this->info("\n📊 Menghitung risiko untuk {$country->name}...");
            
            $data = $this->riskCalculator->calculateForCountry($country);

            \App\Models\RiskScore::create([
                'country_id' => $country->id,
                'weather_risk' => $data['weather_risk'],
                'inflation_risk' => $data['inflation_risk'],
                'currency_risk' => $data['currency_risk'],
                'political_risk' => $data['political_risk'],
                'total_score' => $data['total_score'],
                'date' => now()->toDateString(),
            ]);

            $this->info("  ✅ Total Risk: {$data['total_score']}");

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✅ Selesai menghitung risiko.');
        return 0;
    }
}