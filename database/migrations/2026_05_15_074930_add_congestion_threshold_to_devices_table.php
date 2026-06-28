<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            // Threshold response time (ms) sebelum dianggap "padat"
            // Default 150ms — bisa diubah per perangkat sesuai kondisi
            $table->integer('congestion_threshold_ms')->default(150)->after('is_active');

            // Jumlah cek berturut-turut sebelum alert dikirim
            // Default 5 — artinya 5 x 30 detik = 2.5 menit terus-menerus tinggi
            $table->integer('congestion_check_count')->default(5)->after('congestion_threshold_ms');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['congestion_threshold_ms', 'congestion_check_count']);
        });
    }
};