<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'capital', 'population', 'currency', 'flag', 
        'region', 'latitude', 'longitude'
    ];

    // Relasi yang sudah ada
    public function riskScores()
    {
        return $this->hasMany(RiskScore::class);
    }

    public function news()
    {
        return $this->hasMany(NewsCache::class);
    }

    public function watchlists()
    {
        return $this->hasMany(Watchlist::class);
    }

    public function economicIndicators()
    {
        return $this->hasMany(EconomicIndicator::class)->latest('year');
    }

    public function latestEconomic()
    {
        return $this->hasOne(EconomicIndicator::class)->latest('year');
    }

    public function weatherCache()
    {
        return $this->hasMany(WeatherCache::class)->latest('recorded_at');
    }

    public function latestWeather()
    {
        return $this->hasOne(WeatherCache::class)->latest('recorded_at');
    }

    // Relasi baru untuk kurs
    public function currencyRates()
    {
        return $this->hasMany(CurrencyRate::class)->latest('recorded_at');
    }

    public function latestCurrencyRate()
    {
        return $this->hasOne(CurrencyRate::class)->latest('recorded_at');
    }
}