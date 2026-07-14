<?php

namespace App\Services;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\WeatherCache;
use App\Models\EconomicIndicator;
use App\Models\CurrencyRate;
use App\Models\NewsCache;
use Illuminate\Support\Facades\Log;

class RiskCalculator
{
    protected $sentimentAnalyzer;

    public function __construct(SentimentAnalyzer $sentimentAnalyzer)
    {
        $this->sentimentAnalyzer = $sentimentAnalyzer;
    }

    /**
     * Hitung skor risiko untuk satu negara
     * 
     * @param Country $country
     * @return array
     */
    public function calculateForCountry(Country $country): array
    {
        // Ambil data terbaru
        $weather = $country->latestWeather;
        $economic = $country->latestEconomic;
        $currency = $country->latestCurrencyRate;
        $news = $country->news()->latest()->limit(10)->get();

        // 1. Weather Risk (0-100)
        $weatherRisk = $this->calculateWeatherRisk($weather);

        // 2. Inflation Risk (0-100)
        $inflationRisk = $this->calculateInflationRisk($economic);

        // 3. Currency Risk (0-100)
        $currencyRisk = $this->calculateCurrencyRisk($currency);

        // 4. Political/News Risk (0-100)
        $politicalRisk = $this->calculatePoliticalRisk($news);

        // Total score = weighted average (bobot bisa disesuaikan)
        $totalScore = ($weatherRisk * 0.30) + 
                      ($inflationRisk * 0.20) + 
                      ($currencyRisk * 0.10) + 
                      ($politicalRisk * 0.40);

        $totalScore = round($totalScore, 2);

        return [
            'weather_risk' => $weatherRisk,
            'inflation_risk' => $inflationRisk,
            'currency_risk' => $currencyRisk,
            'political_risk' => $politicalRisk,
            'total_score' => $totalScore,
        ];
    }

    /**
     * Hitung risiko cuaca berdasarkan suhu, kecepatan angin, dan kondisi hujan/badai
     */
    private function calculateWeatherRisk($weather): float
    {
        if (!$weather) {
            return 50; // default medium risk jika data tidak ada
        }

        $risk = 0;

        // Temperatur ekstrem (>35°C atau <0°C)
        if ($weather->temperature !== null) {
            if ($weather->temperature > 35) {
                $risk += 30;
            } elseif ($weather->temperature > 30) {
                $risk += 15;
            } elseif ($weather->temperature < 0) {
                $risk += 30;
            } elseif ($weather->temperature < 10) {
                $risk += 15;
            }
        }

        // Kecepatan angin (> 50 km/h dianggap berisiko)
        if ($weather->wind_speed !== null) {
            if ($weather->wind_speed > 70) {
                $risk += 40;
            } elseif ($weather->wind_speed > 50) {
                $risk += 25;
            } elseif ($weather->wind_speed > 30) {
                $risk += 10;
            }
        }

        // Cuaca buruk berdasarkan kode (hujan/badai)
        $badWeatherCodes = [51, 53, 55, 61, 63, 65, 80, 81, 82, 95, 96, 99];
        if ($weather->weather_code !== null && in_array($weather->weather_code, $badWeatherCodes)) {
            $risk += 20;
        }

        return min(100, $risk);
    }

    /**
     * Hitung risiko inflasi (semakin tinggi inflasi, semakin tinggi risiko)
     */
    private function calculateInflationRisk($economic): float
    {
        if (!$economic || $economic->inflation === null) {
            return 50;
        }

        $inflation = $economic->inflation;

        if ($inflation > 10) {
            return 100;
        } elseif ($inflation > 7) {
            return 80;
        } elseif ($inflation > 5) {
            return 60;
        } elseif ($inflation > 3) {
            return 40;
        } elseif ($inflation > 1) {
            return 20;
        } else {
            return 10;
        }
    }

    /**
     * Hitung risiko kurs: fluktuasi tinggi = risiko tinggi
     * Menggunakan deviasi standar dari beberapa data terakhir (jika ada)
     */
    private function calculateCurrencyRisk($currency): float
    {
        if (!$currency) {
            return 50;
        }

        // Ambil 7 data kurs terakhir untuk negara ini
        $rates = CurrencyRate::where('country_id', $currency->country_id)
            ->orderBy('recorded_at', 'desc')
            ->limit(7)
            ->pluck('rate')
            ->toArray();

        if (count($rates) < 2) {
            // Jika hanya 1 data, asumsikan risiko sedang
            return 50;
        }

        // Hitung standar deviasi sebagai indikator volatilitas
        $mean = array_sum($rates) / count($rates);
        $variance = 0;
        foreach ($rates as $rate) {
            $variance += pow($rate - $mean, 2);
        }
        $variance /= count($rates);
        $stdDev = sqrt($variance);

        // Normalisasi std dev ke skala 0-100 (asumsi max deviasi 10% dari mean)
        $maxDeviation = $mean * 0.1;
        $risk = ($stdDev / $maxDeviation) * 100;
        $risk = min(100, $risk);

        return round($risk, 2);
    }

    /**
     * Hitung risiko politik berdasarkan sentimen berita
     */
    private function calculatePoliticalRisk($news): float
    {
        if ($news->isEmpty()) {
            return 50;
        }

        // Kumpulkan teks berita
        $texts = [];
        foreach ($news as $item) {
            $texts[] = $item->title . ' ' . $item->description;
        }

        // Gabungkan untuk analisis sentimen
        $combinedText = implode(' ', $texts);
        $result = $this->sentimentAnalyzer->analyze($combinedText);

        // Konversi skor sentimen (-1..1) menjadi risiko (0..100)
        // Sentimen negatif -> risiko tinggi
        $score = $result['score']; // -1 (negatif) hingga 1 (positif)
        
        // Mapping: score -1 => 100, 0 => 50, 1 => 0
        $risk = 50 - ($score * 50);
        $risk = max(0, min(100, $risk));

        return round($risk, 2);
    }

    /**
     * Update semua negara
     */
    public function updateAllCountries(): void
    {
        $countries = Country::all();
        
        foreach ($countries as $country) {
            $data = $this->calculateForCountry($country);
            
            RiskScore::create([
                'country_id' => $country->id,
                'weather_risk' => $data['weather_risk'],
                'inflation_risk' => $data['inflation_risk'],
                'currency_risk' => $data['currency_risk'],
                'political_risk' => $data['political_risk'],
                'total_score' => $data['total_score'],
                'date' => now()->toDateString(),
            ]);

            Log::info("Risk score updated for {$country->name}: {$data['total_score']}");
        }
    }
}