<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CurrencyRate;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    protected $exchangeService;

    public function __construct(ExchangeRateService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    /**
     * GET /api/currency/{base}/{target}
     * Kurs terkini antara dua mata uang (langsung dari API atau cache)
     */
    public function show($base, $target)
    {
        // Coba cari di database terlebih dahulu (data terbaru)
        $rate = CurrencyRate::where('base_currency', $base)
            ->where('target_currency', $target)
            ->latest('recorded_at')
            ->first();

        if ($rate) {
            return response()->json([
                'base' => $base,
                'target' => $target,
                'rate' => (float) $rate->rate,
                'recorded_at' => $rate->recorded_at->toDateTimeString(),
                'source' => 'cache',
            ]);
        }

        // Jika tidak ada di cache, ambil langsung dari API
        $newRate = $this->exchangeService->getRate($base, $target);

        if ($newRate !== null) {
            // Simpan ke database untuk cache
            $country = \App\Models\Country::where('currency', $target)->first();
            if ($country) {
                CurrencyRate::create([
                    'country_id' => $country->id,
                    'base_currency' => $base,
                    'target_currency' => $target,
                    'rate' => $newRate,
                    'recorded_at' => now(),
                ]);
            }

            return response()->json([
                'base' => $base,
                'target' => $target,
                'rate' => $newRate,
                'recorded_at' => now()->toDateTimeString(),
                'source' => 'api',
            ]);
        }

        return response()->json([
            'message' => 'Unable to fetch currency rate',
        ], 500);
    }

    /**
     * GET /api/currency/history/{base}/{target}
     * Riwayat kurs (misal 7 hari terakhir) untuk grafik
     */
    public function history($base, $target, Request $request)
    {
        $days = $request->get('days', 7);

        $rates = CurrencyRate::where('base_currency', $base)
            ->where('target_currency', $target)
            ->where('recorded_at', '>=', now()->subDays($days))
            ->orderBy('recorded_at', 'asc')
            ->get();

        return response()->json([
            'base' => $base,
            'target' => $target,
            'history' => $rates->map(fn($r) => [
                'date' => $r->recorded_at->toDateString(),
                'rate' => (float) $r->rate,
            ]),
        ]);
    }
}