<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CurrencyRate;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CurrencyController extends Controller
{
    protected $exchangeService;

    public function __construct(ExchangeRateService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    /**
     * GET /api/currency/{base}/{target}
     */
    public function show($base, $target)
    {
        // Cegah bentrok dengan 'latest'
        if ($base === 'latest' || $target === 'latest') {
            return response()->json(['message' => 'Invalid currency code'], 400);
        }

        try {
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

            $newRate = $this->exchangeService->getRate($base, $target);

            if ($newRate !== null) {
                $country = Country::where('currency', $target)->first();
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
                'message' => 'Unable to fetch currency rate from API. Please try again later.',
                'base' => $base,
                'target' => $target,
                'rate' => null,
            ], 200);

        } catch (\Exception $e) {
            Log::error('CurrencyController@show error: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching currency rate'], 500);
        }
    }

    /**
     * GET /api/currency/history/{base}/{target}
     */
    public function history($base, $target, Request $request)
    {
        try {
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

        } catch (\Exception $e) {
            Log::error('CurrencyController@history error: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching history', 'history' => []], 200);
        }
    }

    /**
     * GET /api/currency/latest/{base?}
     */
    public function latestRates($base = 'USD')
    {
        try {
            $rates = CurrencyRate::with('country')
                ->where('base_currency', $base)
                ->latest('recorded_at')
                ->get()
                ->groupBy('country_id')
                ->map(fn($group) => $group->first())
                ->values();

            if ($rates->isNotEmpty()) {
                return response()->json($rates->map(fn($rate) => [
                    'country' => $rate->country->name,
                    'code' => $rate->country->code,
                    'currency' => $rate->target_currency,
                    'rate' => (float) $rate->rate,
                    'recorded_at' => $rate->recorded_at->toDateTimeString(),
                ]));
            }

            return response()->json([
                'message' => 'Belum ada data kurs. Jalankan php artisan app:fetch-rates USD',
                'data' => []
            ], 200);

        } catch (\Exception $e) {
            Log::error('CurrencyController@latestRates error: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memuat data kurs', 'data' => []], 200);
        }
    }
}