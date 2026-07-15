<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\CurrencyRate;

class CurrencyDashboardController extends Controller
{
    public function index()
    {
        // Ambil daftar negara yang memiliki mata uang dan data kurs
        $countries = Country::whereNotNull('currency')
            ->orderBy('name')
            ->get();

        // Daftar base currency yang tersedia (ambil dari data currency_rates)
        $baseCurrencies = CurrencyRate::distinct('base_currency')->pluck('base_currency')->toArray();
        if (empty($baseCurrencies)) {
            $baseCurrencies = ['USD', 'EUR', 'GBP', 'IDR']; // fallback
        }

        return view('currency.index', compact('countries', 'baseCurrencies'));
    }
}