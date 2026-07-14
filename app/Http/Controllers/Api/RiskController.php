<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RiskResource;
use App\Models\Country;
use App\Models\RiskScore;
use Illuminate\Http\Request;

class RiskController extends Controller
{
    /**
     * GET /api/risk/{country}
     * Skor risiko terkini untuk suatu negara (berdasarkan kode negara)
     */
    public function show($countryCode)
    {
        $country = Country::where('code', $countryCode)->first();

        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        $risk = RiskScore::with('country')
            ->where('country_id', $country->id)
            ->latest('date')
            ->first();

        if (!$risk) {
            return response()->json(['message' => 'Risk data not found for this country'], 404);
        }

        return new RiskResource($risk);
    }

    /**
     * GET /api/risk
     * (Opsional) Daftar semua skor risiko terbaru untuk semua negara
     */
    public function index()
    {
        $risks = RiskScore::with('country')
            ->latest('date')
            ->get()
            ->groupBy('country_id')
            ->map(fn($group) => $group->first())
            ->values();

        return RiskResource::collection($risks);
    }
}