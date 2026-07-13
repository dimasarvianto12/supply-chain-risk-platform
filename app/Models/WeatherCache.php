<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherCache extends Model
{
    use HasFactory;

    // 🔧 FIX: Tentukan nama tabel yang benar (singular)
    protected $table = 'weather_cache';

    protected $fillable = [
        'country_id', 'temperature', 'humidity', 'wind_speed', 'weather_code', 'weather_description', 'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}