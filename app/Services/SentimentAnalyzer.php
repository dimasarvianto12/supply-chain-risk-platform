<?php

namespace App\Services;

use App\Models\PositiveWord;
use App\Models\NegativeWord;

class SentimentAnalyzer
{
    /**
     * Analisis sentimen teks menggunakan lexicon-based approach
     * 
     * @param string $text
     * @return array ['sentiment' => 'positive'|'neutral'|'negative', 'score' => int]
     */
    public function analyze(string $text): array
    {
        // Ambil semua kata positif dan negatif dari database
        $positiveWords = PositiveWord::pluck('word')->toArray();
        $negativeWords = NegativeWord::pluck('word')->toArray();

        // Bersihkan teks: lowercase, hapus tanda baca, split menjadi array kata
        $cleanText = strtolower(preg_replace('/[^\w\s]/', '', $text));
        $words = explode(' ', $cleanText);

        $positiveCount = 0;
        $negativeCount = 0;

        foreach ($words as $word) {
            if (in_array($word, $positiveWords)) {
                $positiveCount++;
            }
            if (in_array($word, $negativeWords)) {
                $negativeCount++;
            }
        }

        // Tentukan sentimen
        if ($positiveCount > $negativeCount) {
            $sentiment = 'positive';
        } elseif ($negativeCount > $positiveCount) {
            $sentiment = 'negative';
        } else {
            $sentiment = 'neutral';
        }

        // Skor sentimen: -1 (negatif) hingga 1 (positif)
        $total = $positiveCount + $negativeCount;
        $score = $total > 0 ? ($positiveCount - $negativeCount) / $total : 0;

        return [
            'sentiment' => $sentiment,
            'score' => $score,
            'positive_count' => $positiveCount,
            'negative_count' => $negativeCount,
        ];
    }

    /**
     * Analisis sentimen untuk kumpulan berita dan hitung rata-rata skor
     * 
     * @param array $articles (array of strings)
     * @return array ['sentiment' => 'positive'|'neutral'|'negative', 'average_score' => float]
     */
    public function analyzeMultiple(array $articles): array
    {
        if (empty($articles)) {
            return ['sentiment' => 'neutral', 'average_score' => 0];
        }

        $totalScore = 0;
        $sentimentCounts = ['positive' => 0, 'neutral' => 0, 'negative' => 0];

        foreach ($articles as $article) {
            $result = $this->analyze($article);
            $totalScore += $result['score'];
            $sentimentCounts[$result['sentiment']]++;
        }

        $averageScore = $totalScore / count($articles);
        
        // Tentukan sentimen keseluruhan
        if ($sentimentCounts['positive'] > $sentimentCounts['negative']) {
            $overall = 'positive';
        } elseif ($sentimentCounts['negative'] > $sentimentCounts['positive']) {
            $overall = 'negative';
        } else {
            $overall = 'neutral';
        }

        return [
            'sentiment' => $overall,
            'average_score' => $averageScore,
        ];
    }
}