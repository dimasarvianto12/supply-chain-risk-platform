<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsCache;
use App\Services\SentimentAnalyzer;

class AnalyzeNewsSentiment extends Command
{
    protected $signature = 'app:analyze-sentiment {--limit=100 : Jumlah berita yang diproses per batch}';
    protected $description = 'Analisis sentimen untuk berita yang belum memiliki sentimen';

    protected $sentimentAnalyzer;

    public function __construct(SentimentAnalyzer $sentimentAnalyzer)
    {
        parent::__construct();
        $this->sentimentAnalyzer = $sentimentAnalyzer;
    }

    public function handle()
    {
        $limit = $this->option('limit');

        // Ambil berita yang belum memiliki sentimen (null)
        $news = NewsCache::whereNull('sentiment')
            ->orWhere('sentiment', '')
            ->limit($limit)
            ->get();

        if ($news->isEmpty()) {
            $this->info('✅ Semua berita sudah memiliki sentimen.');
            return 0;
        }

        $bar = $this->output->createProgressBar($news->count());
        $bar->start();

        foreach ($news as $item) {
            // Gabungkan judul dan deskripsi untuk analisis
            $text = $item->title . ' ' . ($item->description ?? '');
            $result = $this->sentimentAnalyzer->analyze($text);
            
            // Update sentimen
            $item->sentiment = $result['sentiment'];
            $item->save();

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✅ Selesai menganalisis sentimen untuk ' . $news->count() . ' berita.');
        return 0;
    }
}