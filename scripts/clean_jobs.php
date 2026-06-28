<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$targets = [31, 33];
$deleted = [];
$jobs = \DB::table('jobs')->get();
foreach ($jobs as $job) {
    $p = $job->payload;
    foreach ($targets as $t) {
        if (strpos($p, 's:8:"deviceId";i:' . $t) !== false || strpos($p, '"deviceId";i:' . $t) !== false) {
            \DB::table('jobs')->where('id', $job->id)->delete();
            $deleted[] = ['job_id' => $job->id, 'device_id' => $t];
            break;
        }
    }
}
$remaining = \DB::table('jobs')->count();
echo json_encode(['deleted' => $deleted, 'remaining_jobs' => $remaining], JSON_PRETTY_PRINT) . "\n";
