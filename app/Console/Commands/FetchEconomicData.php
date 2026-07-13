<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\EconomicIndicator;
use App\Services\WorldBankService;

class FetchEconomicData extends Command
{
    protected $signature = 'app:fetch-economy {country? : Kode negara (opsional)}';
    protected $description = 'Ambil data GDP dan Inflasi dari World Bank API';

    protected $worldBankService;

    public function __construct(WorldBankService $worldBankService)
    {
        parent::__construct();
        $this->worldBankService = $worldBankService;
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

            $economic = $this->worldBankService->getEconomicData($country->code);

            if ($economic) {
                EconomicIndicator::create([
                    'country_id' => $country->id,
                    'gdp' => $economic['gdp'],
                    'inflation' => $economic['inflation'],
                    'year' => $economic['year'],
                ]);

                $this->info("  ✅ GDP: {$economic['gdp']}, Inflasi: {$economic['inflation']}%");
            } else {
                $this->warn("  ⚠️ Gagal mengambil data ekonomi.");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✅ Selesai memperbarui data ekonomi.');
        return 0;
    }
}