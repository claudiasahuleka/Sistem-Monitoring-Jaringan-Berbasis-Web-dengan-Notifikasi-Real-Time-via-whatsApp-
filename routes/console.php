<?php

use Illuminate\Support\Facades\Schedule;

// Jalankan monitoring setiap menit
Schedule::command('monitor:devices')->everyMinute();

// Alternatif: jalankan setiap 2 menit
// Schedule::command('monitor:devices')->everyTwoMinutes();