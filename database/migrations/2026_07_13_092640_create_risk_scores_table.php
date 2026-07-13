<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->float('weather_risk')->default(0);
            $table->float('inflation_risk')->default(0);
            $table->float('currency_risk')->default(0);
            $table->float('political_risk')->default(0);
            $table->float('total_score')->default(0);
            $table->date('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};