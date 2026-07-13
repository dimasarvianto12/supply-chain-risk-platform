<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'capital', 'population', 'currency', 'flag', 'latitude', 'longitude'
    ];

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
}