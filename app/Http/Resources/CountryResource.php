<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'capital' => $this->capital,
            'population' => $this->population,
            'currency' => $this->currency,
            'flag' => $this->flag,
            'region' => $this->region,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'weather' => $this->whenLoaded('latestWeather', function () {
                return [
                    'temperature' => $this->latestWeather->temperature ?? null,
                    'humidity' => $this->latestWeather->humidity ?? null,
                    'wind_speed' => $this->latestWeather->wind_speed ?? null,
                    'description' => $this->latestWeather->weather_description ?? null,
                ];
            }),
            'economic' => $this->whenLoaded('latestEconomic', function () {
                return [
                    'gdp' => $this->latestEconomic->gdp ?? null,
                    'inflation' => $this->latestEconomic->inflation ?? null,
                    'year' => $this->latestEconomic->year ?? null,
                ];
            }),
            'currency_rate' => $this->whenLoaded('latestCurrencyRate', function () {
                return [
                    'rate' => $this->latestCurrencyRate->rate ?? null,
                    'base' => $this->latestCurrencyRate->base_currency ?? null,
                    'target' => $this->latestCurrencyRate->target_currency ?? null,
                ];
            }),
            'risk_score' => $this->whenLoaded('latestRiskScore', function () {
                return [
                    'total' => $this->latestRiskScore->total_score ?? null,
                    'weather' => $this->latestRiskScore->weather_risk ?? null,
                    'inflation' => $this->latestRiskScore->inflation_risk ?? null,
                    'currency' => $this->latestRiskScore->currency_risk ?? null,
                    'political' => $this->latestRiskScore->political_risk ?? null,
                ];
            }),
        ];
    }
}