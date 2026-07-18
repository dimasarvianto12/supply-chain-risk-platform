<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    /**
     * GET /api/compare/{countryA}/{countryB}
     * Mengembalikan data perbandingan untuk dua negara
     */
    public function compare($countryA, $countryB)
    {
        $country1 = Country::with([
            'latestWeather',
            'latestEconomic',
            'latestCurrencyRate',
            'latestRiskScore'
        ])->where('code', $countryA)->first();

        $country2 = Country::with([
            'latestWeather',
            'latestEconomic',
            'latestCurrencyRate',
            'latestRiskScore'
        ])->where('code', $countryB)->first();

        if (!$country1 || !$country2) {
            return response()->json(['error' => 'Salah satu negara tidak ditemukan'], 404);
        }

        return response()->json([
            'country1' => $this->formatCountryData($country1),
            'country2' => $this->formatCountryData($country2),
        ]);
    }

    private function formatCountryData($country)
    {
        return [
            'code' => $country->code,
            'name' => $country->name,
            'flag' => $country->flag,
            'capital' => $country->capital,
            'population' => $country->population,
            'currency' => $country->currency,
            'weather' => $country->latestWeather ? [
                'temperature' => $country->latestWeather->temperature,
                'humidity' => $country->latestWeather->humidity,
                'wind_speed' => $country->latestWeather->wind_speed,
                'description' => $country->latestWeather->weather_description,
            ] : null,
            'economic' => $country->latestEconomic ? [
                'gdp' => $country->latestEconomic->gdp,
                'inflation' => $country->latestEconomic->inflation,
                'year' => $country->latestEconomic->year,
            ] : null,
            'currency_rate' => $country->latestCurrencyRate ? [
                'rate' => $country->latestCurrencyRate->rate,
                'base' => $country->latestCurrencyRate->base_currency,
                'target' => $country->latestCurrencyRate->target_currency,
            ] : null,
            'risk' => $country->latestRiskScore ? [
                'total' => $country->latestRiskScore->total_score,
                'weather' => $country->latestRiskScore->weather_risk,
                'inflation' => $country->latestRiskScore->inflation_risk,
                'currency' => $country->latestRiskScore->currency_risk,
                'political' => $country->latestRiskScore->political_risk,
            ] : null,
        ];
    }
}