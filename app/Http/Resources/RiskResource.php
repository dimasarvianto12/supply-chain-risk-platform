<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'country' => $this->country->name,
            'country_code' => $this->country->code,
            'weather_risk' => $this->weather_risk,
            'inflation_risk' => $this->inflation_risk,
            'currency_risk' => $this->currency_risk,
            'political_risk' => $this->political_risk,
            'total_score' => $this->total_score,
            'date' => $this->date->toDateString(),
        ];
    }
}