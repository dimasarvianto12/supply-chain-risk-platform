<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $favorites = $user->favoriteCountries()
            ->with([
                'latestWeather',
                'latestEconomic',
                'latestCurrencyRate',
                'latestRiskScore'
            ])
            ->get();

        return view('favorites.index', compact('favorites'));
    }
}