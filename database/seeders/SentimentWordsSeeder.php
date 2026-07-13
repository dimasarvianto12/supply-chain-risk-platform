<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PositiveWord;
use App\Models\NegativeWord;

class SentimentWordsSeeder extends Seeder
{
    public function run(): void
    {
        $positive = ['growth', 'increase', 'profit', 'stable', 'improve', 'surge', 'gain', 'recovery', 'boom', 'upswing'];
        foreach ($positive as $word) {
            PositiveWord::updateOrCreate(['word' => $word]);
        }

        $negative = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'decline', 'drop', 'conflict', 'sanction', 'crash'];
        foreach ($negative as $word) {
            NegativeWord::updateOrCreate(['word' => $word]);
        }
    }
}