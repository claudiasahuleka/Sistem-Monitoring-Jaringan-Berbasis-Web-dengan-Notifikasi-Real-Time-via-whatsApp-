<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$wa = $app->make(App\Services\WhatsAppService::class);
$res = $wa->sendStableAlert('TEST_DEVICE', '127.0.0.1', 'LOCAL', 123);
echo json_encode($res, JSON_PRETTY_PRINT) . "\n";
