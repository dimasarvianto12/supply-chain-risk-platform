<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'sentiment' => $this->sentiment,
            'url' => $this->url,
            'published_at' => $this->published_at?->toDateTimeString(),
            'country' => $this->country->name,
        ];
    }
}