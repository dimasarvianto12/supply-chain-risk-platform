<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsCache extends Model
{
    use HasFactory;

    protected $table = 'news_cache';

    protected $fillable = [
        'country_id', 'title', 'description', 'sentiment', 'url', 'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}