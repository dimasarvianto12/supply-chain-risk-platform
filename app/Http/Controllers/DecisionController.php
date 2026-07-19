<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use Illuminate\Http\Request;

class DecisionController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('name')->get();
        // Hanya ambil port yang negaranya ada di database kita
        $ports = Port::orderBy('name')->get();
        return view('decision.index', compact('countries', 'ports'));
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'origin_country_id' => 'required|exists:countries,id',
            'destination_country_id' => 'required|exists:countries,id',
            'origin_port_id' => 'required|exists:ports,id',
            'destination_port_id' => 'required|exists:ports,id',
        ]);

        $originCountry = Country::with('latestRiskScore')->find($request->origin_country_id);
        $destCountry = Country::with('latestRiskScore')->find($request->destination_country_id);
        
        $originPort = Port::find($request->origin_port_id);
        $destPort = Port::find($request->destination_port_id);

        $originRisk = $originCountry->latestRiskScore;
        $destRisk = $destCountry->latestRiskScore;

        if (!$originRisk || !$destRisk) {
            return response()->json(['error' => 'Data risiko belum lengkap untuk negara ini.'], 400);
        }

        // Kalkulasi Hambatan (Kombinasi)
        $weatherRisk = ($originRisk->weather_risk + $destRisk->weather_risk) / 2;
        $inflationRisk = ($originRisk->inflation_risk + $destRisk->inflation_risk) / 2;
        $currencyRisk = ($originRisk->currency_risk + $destRisk->currency_risk) / 2;
        $politicalRisk = ($originRisk->political_risk + $destRisk->political_risk) / 2;
        
        // Port Congestion Risk (0 to 100)
        $originPortRisk = $this->getCongestionScore($originPort->congestion_level);
        $destPortRisk = $this->getCongestionScore($destPort->congestion_level);
        $portRisk = ($originPortRisk + $destPortRisk) / 2;
        $totalDelayDays = $originPort->delay_days + $destPort->delay_days;

        // Total Risk Score (Rata-rata 5 Pilar)
        $totalRisk = ($weatherRisk + $inflationRisk + $currencyRisk + $politicalRisk + $portRisk) / 5;

        // Recommendation Logic
        $status = 'Safe';
        $color = 'success';
        $message = 'Kondisi sangat kondusif untuk melakukan impor. Tidak ada hambatan yang signifikan.';

        if ($totalRisk > 70) {
            $status = 'Critical';
            $color = 'danger';
            $message = "Sangat Berisiko: Tunda impor. Risiko keseluruhan terlalu tinggi ($totalRisk/100). ";
            if ($totalDelayDays >= 7) $message .= "Pelabuhan mengalami kemacetan parah dengan estimasi delay total $totalDelayDays hari. ";
            if ($weatherRisk > 70) $message .= "Cuaca sangat ekstrem berpotensi menggagalkan pelayaran. ";
        } elseif ($totalRisk > 40) {
            $status = 'Warning';
            $color = 'warning';
            $message = "Waspada: Anda dapat melanjutkan impor, namun perhatikan hambatan berikut: ";
            if ($totalDelayDays >= 3) $message .= "Ada delay pelabuhan sekitar $totalDelayDays hari. ";
            if ($currencyRisk > 60) $message .= "Fluktuasi nilai tukar cukup tinggi, pertimbangkan hedging. ";
            if ($politicalRisk > 60) $message .= "Sentimen berita/geopolitik kurang baik. ";
        }

        // Kalkulasi Jarak & Estimasi Waktu Pengiriman (Haversine Formula)
        $distanceKm = $this->calculateDistance($originPort->latitude, $originPort->longitude, $destPort->latitude, $destPort->longitude);
        // Asumsi kecepatan rata-rata kapal kargo: 20 knots (~900 km / hari)
        $baseShippingDays = $distanceKm > 0 ? ceil($distanceKm / 900) : 0;
        $totalEstimatedDays = $baseShippingDays + $totalDelayDays;

        return response()->json([
            'totalRisk' => round($totalRisk, 2),
            'weatherRisk' => round($weatherRisk, 2),
            'inflationRisk' => round($inflationRisk, 2),
            'currencyRisk' => round($currencyRisk, 2),
            'politicalRisk' => round($politicalRisk, 2),
            'portRisk' => round($portRisk, 2),
            'shipping' => [
                'distance_km' => number_format($distanceKm, 0),
                'base_days' => $baseShippingDays,
                'total_days' => $totalEstimatedDays
            ],
            'origin' => [
                'country' => $originCountry->name,
                'port' => $originPort->name,
                'congestion' => $originPort->congestion_level,
                'delay' => $originPort->delay_days
            ],
            'destination' => [
                'country' => $destCountry->name,
                'port' => $destPort->name,
                'congestion' => $destPort->congestion_level,
                'delay' => $destPort->delay_days
            ],
            'recommendation' => [
                'status' => $status,
                'color' => $color,
                'message' => $message
            ]
        ]);
    }

    private function getCongestionScore($level)
    {
        return match ($level) {
            'high' => 90,
            'medium' => 50,
            default => 10,
        };
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) 
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * asin(sqrt($a));
        $distance = $earthRadius * $c;

        return $distance;
    }
}
