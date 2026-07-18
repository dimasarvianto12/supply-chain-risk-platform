<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ==========================================
    // RELASI UNTUK FAVORIT (WATCHLIST)
    // ==========================================

    /**
     * Relasi one-to-many ke tabel watchlists
     * Mendapatkan semua entri watchlist milik user
     */
    public function watchlists()
    {
        return $this->hasMany(Watchlist::class);
    }

    /**
     * Relasi many-to-many ke tabel countries melalui watchlists
     * Mendapatkan semua negara yang difavoritkan oleh user
     * 
     * ✅ HANYA SATU DEFINISI (tidak ada duplikasi!)
     */
    public function favoriteCountries()
    {
        return $this->belongsToMany(Country::class, 'watchlists')
            ->withTimestamps() // otomatis mengisi created_at dan updated_at di pivot
            ->orderBy('watchlists.created_at', 'desc'); // urutkan berdasarkan waktu tambah terbaru
    }

    /**
     * Cek apakah user memiliki negara tertentu di favorit
     * 
     * @param int|string $countryId
     * @return bool
     */
    public function hasFavorite($countryId)
    {
        return $this->favoriteCountries()->where('country_id', $countryId)->exists();
    }

    /**
     * Tambahkan negara ke favorit
     * 
     * @param int|string $countryId
     * @return \App\Models\Watchlist|null
     */
    public function addFavorite($countryId)
    {
        if (!$this->hasFavorite($countryId)) {
            return $this->favoriteCountries()->attach($countryId);
        }
        return null;
    }

    /**
     * Hapus negara dari favorit
     * 
     * @param int|string $countryId
     * @return int
     */
    public function removeFavorite($countryId)
    {
        return $this->favoriteCountries()->detach($countryId);
    }

    public function isAdmin()
    {
        return $this->is_admin == 1;
    }
}