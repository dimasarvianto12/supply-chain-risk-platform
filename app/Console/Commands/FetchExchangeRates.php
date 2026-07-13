<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\CurrencyRate;
use App\Services\ExchangeRateService;

class FetchExchangeRates extends Command
{
    protected $signature = 'app:fetch-rates {base=USD : Mata uang dasar (default USD)}';
    protected $description = 'Ambil kurs mata uang dari ExchangeRate API untuk semua negara';

    protected $exchangeService;

    public function __construct(ExchangeRateService $exchangeService)
    {
        parent::__construct();
        $this->exchangeService = $exchangeService;
    }

    public function handle()
    {
        $baseCurrency = $this->argument('base');

        // Ambil semua negara yang punya kode mata uang (currency tidak null)
        $countries = Country::whereNotNull('currency')->get();

        if ($countries->isEmpty()) {
            $this->error('Tidak ada negara dengan mata uang terdaftar.');
            return 1;
        }

        $bar = $this->output->createProgressBar($countries->count());
        $bar->start();

        foreach ($countries as $country) {
            $targetCurrency = $country->currency;

            // Jika mata uangnya sama dengan base, skip atau rate = 1
            if ($targetCurrency === $baseCurrency) {
                $this->info("\n{$country->name}: rate 1.000000 (sama dengan base)");
                $bar->advance();
                continue;
            }

            $this->info("\nMemproses {$country->name} ({$targetCurrency})...");

            $rate = $this->exchangeService->getRate($baseCurrency, $targetCurrency);

            if ($rate !== null) {
                CurrencyRate::create([
                    'country_id' => $country->id,
                    'base_currency' => $baseCurrency,
                    'target_currency' => $targetCurrency,
                    'rate' => $rate,
                    'recorded_at' => now(),
                ]);

                $this->info("  ✅ 1 {$baseCurrency} = {$rate} {$targetCurrency}");
            } else {
                $this->warn("  ⚠️ Gagal mengambil kurs.");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✅ Selesai memperbarui kurs mata uang.');
        return 0;
    }
}