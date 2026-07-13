<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EconomicIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id', 'gdp', 'inflation', 'year'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}