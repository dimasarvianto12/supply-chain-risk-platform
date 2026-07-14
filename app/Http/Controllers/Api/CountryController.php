<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::with([
            'latestWeather',
            'latestEconomic',
            'latestCurrencyRate',
            'latestRiskScore'
        ])->get();

        return CountryResource::collection($countries);
    }

    public function show($code)
    {
        $country = Country::with([
            'latestWeather',
            'latestEconomic',
            'latestCurrencyRate',
            'latestRiskScore'
        ])->where('code', $code)->first();

        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        return new CountryResource($country);
    }
}