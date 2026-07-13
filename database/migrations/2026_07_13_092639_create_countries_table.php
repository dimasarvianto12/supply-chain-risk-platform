<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // ISO kode negara (ID, US, dll)
            $table->string('name');
            $table->string('capital')->nullable();
            $table->bigInteger('population')->nullable();
            $table->string('currency')->nullable();
            $table->string('flag')->nullable(); // URL flag
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};