<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            // Kolom sudah ada: code, name, capital, population, currency, flag, latitude, longitude
            // Kita hanya tambahkan jika belum ada (sebagai safety)
            if (!Schema::hasColumn('countries', 'capital')) {
                $table->string('capital')->nullable()->after('name');
            }
            if (!Schema::hasColumn('countries', 'flag')) {
                $table->string('flag')->nullable()->after('currency');
            }
            // Tambahkan juga region/wilayah (opsional)
            if (!Schema::hasColumn('countries', 'region')) {
                $table->string('region')->nullable()->after('flag');
            }
        });
    }

    public function down()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['capital', 'flag', 'region']);
        });
    }
};