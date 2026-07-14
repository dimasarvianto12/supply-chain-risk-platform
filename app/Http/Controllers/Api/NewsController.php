<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Models\Country;
use App\Models\NewsCache;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * GET /api/news/{country}
     * Berita untuk suatu negara (dengan sentimen)
     */
    public function show($countryCode, Request $request)
    {
        $country = Country::where('code', $countryCode)->first();

        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        $limit = $request->get('limit', 20);

        $news = NewsCache::where('country_id', $country->id)
            ->with('country')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        return NewsResource::collection($news);
    }

    /**
     * GET /api/news
     * (Opsional) Berita global dengan filter kata kunci
     */
    public function index(Request $request)
    {
        $query = NewsCache::with('country');

        if ($request->has('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                  ->orWhere('description', 'like', '%' . $keyword . '%');
            });
        }

        if ($request->has('sentiment')) {
            $query->where('sentiment', $request->sentiment);
        }

        $limit = $request->get('limit', 30);
        $news = $query->orderBy('published_at', 'desc')->limit($limit)->get();

        return NewsResource::collection($news);
    }
}