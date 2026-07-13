<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id', 'weather_risk', 'inflation_risk', 'currency_risk', 'political_risk', 'total_score', 'date'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}