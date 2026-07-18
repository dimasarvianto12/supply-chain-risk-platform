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
     * GET /api/news
     * Berita global dengan filter kata kunci, negara, dan sentimen
     */
    public function index(Request $request)
    {
        $query = NewsCache::with('country');

        // Filter berdasarkan negara (kode negara)
        if ($request->has('country') && $request->country) {
            $country = Country::where('code', $request->country)->first();
            if ($country) {
                $query->where('country_id', $country->id);
            }
        }

        // Filter berdasarkan kata kunci (judul atau deskripsi)
        if ($request->has('keyword') && $request->keyword) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                  ->orWhere('description', 'like', '%' . $keyword . '%');
            });
        }

        // Filter berdasarkan sentimen
        if ($request->has('sentiment') && $request->sentiment) {
            $sentiment = strtolower($request->sentiment);
            $query->where('sentiment', $sentiment);
        }

        // Pagination (10 item per halaman)
        $perPage = $request->get('per_page', 10);
        $news = $query->orderBy('published_at', 'desc')->paginate($perPage);

        return NewsResource::collection($news);
    }

    /**
     * GET /api/news/{country}
     * Berita untuk satu negara tertentu (dengan sentimen)
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
}