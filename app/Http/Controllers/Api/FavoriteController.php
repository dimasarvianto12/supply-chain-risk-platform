<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * GET /api/favorites
     * Daftar favorit user
     */
    public function index()
    {
        $user = Auth::user();
        
        // Jika user tidak login, return 401
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $favorites = $user->favoriteCountries()
            ->with([
                'latestWeather',
                'latestEconomic',
                'latestCurrencyRate',
                'latestRiskScore'
            ])
            ->get();

        return response()->json($favorites->map(function ($country) {
            return [
                'id' => $country->id,
                'code' => $country->code,
                'name' => $country->name,
                'flag' => $country->flag,
                'currency' => $country->currency,
                'weather' => $country->latestWeather ? [
                    'temperature' => $country->latestWeather->temperature,
                    'description' => $country->latestWeather->weather_description,
                ] : null,
                'risk' => $country->latestRiskScore ? [
                    'total' => $country->latestRiskScore->total_score,
                ] : null,
                'added_at' => $country->pivot->created_at,
            ];
        }));
    }

    /**
     * POST /api/favorites/{country}
     * Tambahkan negara ke favorit
     */
    public function store($countryCode)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $country = Country::where('code', $countryCode)->first();
        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        // Cek apakah sudah ada
        $exists = Watchlist::where('user_id', $user->id)
            ->where('country_id', $country->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Country already in favorites',
                'favorited' => true,
            ]);
        }

        Watchlist::create([
            'user_id' => $user->id,
            'country_id' => $country->id,
        ]);

        return response()->json([
            'message' => 'Country added to favorites',
            'favorited' => true,
        ]);
    }

    /**
     * DELETE /api/favorites/{country}
     * Hapus negara dari favorit
     */
    public function destroy($countryCode)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $country = Country::where('code', $countryCode)->first();
        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        $deleted = Watchlist::where('user_id', $user->id)
            ->where('country_id', $country->id)
            ->delete();

        if ($deleted) {
            return response()->json([
                'message' => 'Country removed from favorites',
                'favorited' => false,
            ]);
        }

        return response()->json([
            'message' => 'Country not in favorites',
            'favorited' => false,
        ], 404);
    }

    /**
     * GET /api/favorites/check/{country}
     * Cek apakah negara ada di favorit user
     */
    public function check($countryCode)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $country = Country::where('code', $countryCode)->first();
        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        $exists = Watchlist::where('user_id', $user->id)
            ->where('country_id', $country->id)
            ->exists();

        return response()->json([
            'favorited' => $exists,
        ]);
    }
}