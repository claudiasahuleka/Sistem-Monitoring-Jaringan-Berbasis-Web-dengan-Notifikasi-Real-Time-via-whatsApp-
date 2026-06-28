<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$controller = app(App\Http\Controllers\MonitoringApiController::class);
$res = $controller->runCheck();
echo $res->getContent() . "\n";
