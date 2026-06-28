<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$rows = \App\Models\NotificationLog::with('device')->latest()->take(10)->get()->map(function ($n) {
    return [
        'id' => $n->id,
        'device_id' => $n->device_id,
        'device_name' => $n->device?->name,
        'wa_number' => $n->wa_number,
        'type' => $n->type,
        'is_sent' => (bool) $n->is_sent,
    ];
})->toArray();
echo "Latest 10 notifications:\n";
echo json_encode($rows, JSON_PRETTY_PRINT) . "\n\n";

$nulls = \App\Models\NotificationLog::whereNull('wa_number')->orWhere('wa_number', '')->count();
echo "NotificationLogs with null/empty wa_number: {$nulls}\n";

if ($nulls > 0) {
    $sample = \App\Models\NotificationLog::whereNull('wa_number')->orWhere('wa_number', '')->latest()->take(10)->get(['id', 'device_id', 'type', 'is_sent'])->toArray();
    echo "Sample null entries:\n" . json_encode($sample, JSON_PRETTY_PRINT) . "\n";
}
