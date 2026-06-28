<?php

namespace App\Http\Middleware;

use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\RateLimiter as RateLimiterFacade;

class ConfigureRateLimiting
{
    /**
     * Configure the rate limiters for the application.
     */
    public function configure(): void
    {
        // Rate limiter untuk API umum (60 requests per menit)
        RateLimiterFacade::for('api', function ($request) {
            return $request->user()
                ? \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user()->id)
                : \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip());
        });

        // Rate limiter untuk WhatsApp notifications
        // Batasi 10 notifikasi per menit untuk mencegah spam API Fonnte
        RateLimiterFacade::for('whatsapp', function ($request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10);
        });

        // Rate limiter untuk monitoring check (30 requests per minute)
        RateLimiterFacade::for('monitoring', function ($request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(30);
        });
    }
}
