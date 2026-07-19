<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\RiskScore;

class DashboardController extends Controller
{
    public function index()
    {
        return view('home');
    }

    /**
     * API endpoint untuk data ringkasan dashboard (digunakan oleh AJAX)
     */
    public function summary()
    {
        $totalCountries = Country::count();
        
        $latestRisks = RiskScore::with('country')
            ->latest('date')
            ->get()
            ->groupBy('country_id')
            ->map(fn($group) => $group->first());

        $avgRisk = $latestRisks->avg('total_score') ?? 0;

        // Ambil 5 negara dengan risiko tertinggi
        $topRisks = $latestRisks->sortByDesc('total_score')->take(5)->values();

        // Hitung Distribusi Risiko & Cuaca Ekstrem
        $lowCount = 0;
        $mediumCount = 0;
        $highCount = 0;
        $extremeWeatherCount = 0;

        foreach ($latestRisks as $risk) {
            if ($risk->total_score < 30) {
                $lowCount++;
            } elseif ($risk->total_score <= 60) {
                $mediumCount++;
            } else {
                $highCount++;
            }

            if ($risk->weather_risk > 60) {
                $extremeWeatherCount++;
            }
        }

        // Hitung Berita Terbaru (NewsCache + Articles)
        $newsCount = \App\Models\NewsCache::count() + \App\Models\Article::count();

        return response()->json([
            'total_countries' => $totalCountries,
            'avg_risk' => round($avgRisk, 2),
            'top_risks' => $topRisks->map(fn($risk) => [
                'country' => $risk->country->name,
                'code' => $risk->country->code,
                'total_score' => $risk->total_score,
            ]),
            'distribution' => [
                'low' => $lowCount,
                'medium' => $mediumCount,
                'high' => $highCount,
            ],
            'extreme_weather_count' => $extremeWeatherCount,
            'news_count' => $newsCount,
        ]);
    }
}