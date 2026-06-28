<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['up', 'down']);
            $table->integer('response_time')->nullable();
            $table->string('ip_address');
            $table->timestamp('checked_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['device_id', 'checked_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_logs');
    }
};