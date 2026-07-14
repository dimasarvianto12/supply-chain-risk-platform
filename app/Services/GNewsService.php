<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GNewsService
{
    protected $baseUrl = 'https://gnews.io/api/v4';

    /**
     * Ambil berita berdasarkan kata kunci (keyword)
     * @param string $keyword
     * @param int $maxResults
     * @return array|null
     */
    public function searchNews(string $keyword, int $maxResults = 10): ?array
    {
        $apiKey = config('services.gnews.api_key');

        if (empty($apiKey)) {
            Log::error("GNEWS API key tidak ditemukan");
            return null;
        }

        try {
            $url = "{$this->baseUrl}/search";
            
            $response = Http::withOptions(['verify' => false])
                ->get($url, [
                    'q' => $keyword,
                    'token' => $apiKey,
                    'lang' => 'en',
                    'max' => $maxResults,
                ]);

            if (!$response->successful()) {
                Log::warning("GNews API gagal untuk keyword: {$keyword}, status: " . $response->status());
                return null;
            }

            $data = $response->json();

            if (!isset($data['articles']) || empty($data['articles'])) {
                return [];
            }

            $articles = [];
            foreach ($data['articles'] as $article) {
                $articles[] = [
                    'title' => $article['title'] ?? 'No title',
                    'description' => $article['description'] ?? '',
                    'url' => $article['url'] ?? '',
                    'published_at' => isset($article['publishedAt']) 
                        ? date('Y-m-d H:i:s', strtotime($article['publishedAt'])) 
                        : now(),
                ];
            }

            return $articles;

        } catch (\Exception $e) {
            Log::error("GNews API error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Ambil berita terkait logistik, trade, shipping, dan ekonomi untuk suatu negara
     * @param string $countryName
     * @return array|null
     */
    public function getCountryNews(string $countryName): ?array
    {
        // Gabungkan beberapa kata kunci untuk mendapatkan hasil yang lebih relevan
        $keywords = [
            "{$countryName} logistics",
            "{$countryName} trade",
            "{$countryName} shipping",
            "{$countryName} economy",
        ];

        $allArticles = [];
        $usedTitles = [];

        foreach ($keywords as $keyword) {
            $articles = $this->searchNews($keyword, 5);
            
            if (!empty($articles)) {
                foreach ($articles as $article) {
                    // Hindari duplikat berdasarkan judul
                    $titleHash = md5($article['title']);
                    if (!in_array($titleHash, $usedTitles)) {
                        $usedTitles[] = $titleHash;
                        $allArticles[] = $article;
                    }
                }
            }

            // Batasi total maksimal 15 artikel per negara
            if (count($allArticles) >= 15) {
                break;
            }
        }

        return $allArticles;
    }
}