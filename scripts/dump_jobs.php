<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$jobs = \DB::table('jobs')->orderBy('id', 'desc')->take(20)->get();
$result = [];
foreach ($jobs as $j) {
    $payload = json_decode($j->payload, true);
    $display = [
        'id' => $j->id,
        'queue' => $j->queue,
        'attempts' => $j->attempts,
        'available_at' => date('c', $j->available_at),
        'reserved_at' => $j->reserved_at ? date('c', $j->reserved_at) : null,
        'created_at' => date('c', $j->created_at),
        'job' => $payload['displayName'] ?? null,
        'data' => $payload['data'] ?? null,
    ];
    $result[] = $display;
}
echo json_encode($result, JSON_PRETTY_PRINT);
