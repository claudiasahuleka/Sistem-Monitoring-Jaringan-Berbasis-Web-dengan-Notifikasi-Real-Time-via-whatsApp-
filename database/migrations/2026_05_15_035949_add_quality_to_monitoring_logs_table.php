<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring_logs', function (Blueprint $table) {
            // Kualitas jaringan berdasarkan response time
            $table->enum('quality', ['good', 'slow', 'very_slow', 'down'])
                  ->default('good')
                  ->after('status');

            // Persentase packet loss (0–100)
            $table->integer('packet_loss')->default(0)->after('quality');
        });

        // Tambahkan tipe notifikasi baru ke notification_logs
        Schema::table('notification_logs', function (Blueprint $table) {
            // Ubah kolom type agar mendukung tipe baru
            $table->string('type')->default('down')->change();
            // Tipe: down, recovery, slow, stable, bandwidth_high
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_logs', function (Blueprint $table) {
            $table->dropColumn(['quality', 'packet_loss']);
        });
    }
};