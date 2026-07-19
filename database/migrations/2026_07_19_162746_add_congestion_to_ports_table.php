<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ports', function (Blueprint $table) {
            $table->string('congestion_level')->default('low'); // low, medium, high
            $table->integer('delay_days')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ports', function (Blueprint $table) {
            $table->dropColumn(['congestion_level', 'delay_days']);
        });
    }
};
