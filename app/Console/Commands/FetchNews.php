<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\NewsCache;
use App\Services\GNewsService;
use App\Services\SentimentAnalyzer;

class FetchNews extends Command
{
    protected $signature = 'app:fetch-news {country? : Kode negara (opsional)}';
    protected $description = 'Ambil berita dari GNews API untuk semua negara atau satu negara tertentu, dan langsung analisis sentimen';

    protected $gnewsService;
    protected $sentimentAnalyzer;

    public function __construct(GNewsService $gnewsService, SentimentAnalyzer $sentimentAnalyzer)
    {
        parent::__construct();
        $this->gnewsService = $gnewsService;
        $this->sentimentAnalyzer = $sentimentAnalyzer;
    }

    public function handle()
    {
        $countryCode = $this->argument('country');

        if ($countryCode) {
            $countries = Country::where('code', $countryCode)->get();
        } else {
            $countries = Country::all();
        }

        if ($countries->isEmpty()) {
            $this->error('❌ Negara tidak ditemukan.');
            return 1;
        }

        $bar = $this->output->createProgressBar($countries->count());
        $bar->start();

        foreach ($countries as $country) {
            $this->info("\n\n📰 Mengambil berita untuk {$country->name} ({$country->code})...");

            $articles = $this->gnewsService->getCountryNews($country->name);

            if ($articles === null) {
                $this->warn("  ⚠️ Gagal mengambil berita (API error).");
                $bar->advance();
                continue;
            }

            if (empty($articles)) {
                $this->line("  ℹ️ Tidak ada berita ditemukan.");
                $bar->advance();
                continue;
            }

            $count = 0;
            foreach ($articles as $article) {
                // Cek apakah berita sudah ada (hindari duplikat berdasarkan judul)
                $exists = NewsCache::where('country_id', $country->id)
                    ->where('title', $article['title'])
                    ->exists();

                if (!$exists) {
                    // Analisis sentimen
                    $text = ($article['title'] ?? '') . ' ' . ($article['description'] ?? '');
                    $sentimentResult = $this->sentimentAnalyzer->analyze($text);

                    NewsCache::create([
                        'country_id' => $country->id,
                        'title' => $article['title'],
                        'description' => $article['description'],
                        'url' => $article['url'],
                        'published_at' => $article['published_at'],
                        'sentiment' => $sentimentResult['sentiment'], // positive, neutral, negative
                    ]);
                    $count++;
                }
            }

            $this->info("  ✅ {$count} berita baru disimpan (dengan sentimen).");

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✅ Selesai mengambil dan menganalisis berita.');
        return 0;
    }
}