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

    // ==========================================
    // RELASI YANG SUDAH ADA
    // ==========================================

    public function riskScores()
    {
        return $this->hasMany(RiskScore::class);
    }

    public function latestRiskScore()
    {
        return $this->hasOne(RiskScore::class)->latest('date');
    }
    
    public function news()
    {
        return $this->hasMany(NewsCache::class);
    }

    // One-to-many ke Watchlist (entri favorit per user)
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

    public function currencyRates()
    {
        return $this->hasMany(CurrencyRate::class)->latest('recorded_at');
    }

    public function latestCurrencyRate()
    {
        return $this->hasOne(CurrencyRate::class)->latest('recorded_at');
    }

    // ==========================================
    // RELASI BARU: MANY-TO-MANY KE USER
    // ==========================================

    /**
     * Relasi many-to-many ke User melalui tabel watchlists
     * Mendapatkan semua user yang memfavoritkan negara ini
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'watchlists')
            ->withTimestamps() // otomatis mengisi created_at dan updated_at di pivot
            ->orderBy('watchlists.created_at', 'desc');
    }

    /**
     * Cek apakah negara ini difavoritkan oleh user tertentu
     * 
     * @param int|User $user
     * @return bool
     */
    public function isFavoritedBy($user)
    {
        if ($user instanceof User) {
            $userId = $user->id;
        } else {
            $userId = $user;
        }
        return $this->favoritedBy()->where('user_id', $userId)->exists();
    }
}