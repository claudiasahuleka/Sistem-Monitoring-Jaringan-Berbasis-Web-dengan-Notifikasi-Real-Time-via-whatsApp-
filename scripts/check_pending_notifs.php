<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$rows = App\Models\NotificationLog::whereNull('sent_at')->orWhere('is_sent', false)
    ->orderByDesc('created_at')
    ->get(['id', 'device_id', 'type', 'is_sent', 'sent_at', 'created_at'])
    ->toArray();
echo json_encode($rows, JSON_PRETTY_PRINT) . "\n";
