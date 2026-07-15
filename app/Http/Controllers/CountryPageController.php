<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;

class CountryPageController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('name')->get();
        return view('countries.index', compact('countries'));
    }

    /**
     * API endpoint untuk detail negara (digunakan oleh AJAX)
     */
    public function detail($code)
    {
        $country = Country::with([
            'latestWeather',
            'latestEconomic',
            'latestCurrencyRate',
            'latestRiskScore'
        ])->where('code', $code)->first();

        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        return response()->json([
            'code' => $country->code,
            'name' => $country->name,
            'capital' => $country->capital,
            'population' => $country->population,
            'currency' => $country->currency,
            'flag' => $country->flag,
            'region' => $country->region,
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
                'recorded_at' => $country->latestCurrencyRate->recorded_at,
            ] : null,
            'risk' => $country->latestRiskScore ? [
                'total' => $country->latestRiskScore->total_score,
                'weather' => $country->latestRiskScore->weather_risk,
                'inflation' => $country->latestRiskScore->inflation_risk,
                'currency' => $country->latestRiskScore->currency_risk,
                'political' => $country->latestRiskScore->political_risk,
            ] : null,
        ]);
    }
}