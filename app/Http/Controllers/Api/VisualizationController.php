<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\EconomicIndicator;
use App\Models\CurrencyRate;
use App\Models\RiskScore;
use Illuminate\Http\Request;

class VisualizationController extends Controller
{
    /**
     * GET /api/visualization/gdp/{country}
     * Data GDP untuk chart (5 tahun terakhir)
     */
    public function gdp($countryCode)
    {
        $country = Country::where('code', $countryCode)->first();
        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        $data = EconomicIndicator::where('country_id', $country->id)
            ->orderBy('year', 'asc')
            ->limit(10)
            ->get(['year', 'gdp']);

        return response()->json([
            'country' => $country->name,
            'data' => $data->map(fn($item) => [
                'year' => $item->year,
                'gdp' => (float) $item->gdp,
            ]),
        ]);
    }

    /**
     * GET /api/visualization/inflation/{country}
     * Data Inflasi untuk chart (5 tahun terakhir)
     */
    public function inflation($countryCode)
    {
        $country = Country::where('code', $countryCode)->first();
        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        $data = EconomicIndicator::where('country_id', $country->id)
            ->orderBy('year', 'asc')
            ->limit(10)
            ->get(['year', 'inflation']);

        return response()->json([
            'country' => $country->name,
            'data' => $data->map(fn($item) => [
                'year' => $item->year,
                'inflation' => (float) $item->inflation,
            ]),
        ]);
    }

    /**
     * GET /api/visualization/currency/{country}
     * Data Kurs untuk chart (7 hari terakhir)
     */
    public function currency($countryCode)
    {
        $country = Country::where('code', $countryCode)->first();
        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        $data = CurrencyRate::where('country_id', $country->id)
            ->orderBy('recorded_at', 'asc')
            ->limit(7)
            ->get(['recorded_at', 'rate', 'base_currency', 'target_currency']);

        return response()->json([
            'country' => $country->name,
            'base' => $data->first()?->base_currency ?? 'USD',
            'target' => $data->first()?->target_currency ?? $country->currency,
            'data' => $data->map(fn($item) => [
                'date' => $item->recorded_at->format('Y-m-d'),
                'rate' => (float) $item->rate,
            ]),
        ]);
    }

    /**
     * GET /api/visualization/risk/{country}
     * Data Risiko untuk chart (30 hari terakhir)
     */
    public function risk($countryCode)
    {
        $country = Country::where('code', $countryCode)->first();
        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        $data = RiskScore::where('country_id', $country->id)
            ->orderBy('date', 'asc')
            ->limit(30)
            ->get(['date', 'total_score', 'weather_risk', 'inflation_risk', 'currency_risk', 'political_risk']);

        return response()->json([
            'country' => $country->name,
            'data' => $data->map(fn($item) => [
                'date' => $item->date->format('Y-m-d'),
                'total' => (float) $item->total_score,
                'weather' => (float) $item->weather_risk,
                'inflation' => (float) $item->inflation_risk,
                'currency' => (float) $item->currency_risk,
                'political' => (float) $item->political_risk,
            ]),
        ]);
    }
}