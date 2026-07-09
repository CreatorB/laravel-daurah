<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "users (active): " . \App\Models\User::count() . PHP_EOL;
echo "users (trashed): " . \App\Models\User::onlyTrashed()->count() . PHP_EOL;
echo "events (active): " . \App\Models\Event::count() . PHP_EOL;
echo "events (trashed): " . \App\Models\Event::onlyTrashed()->count() . PHP_EOL;
echo "recycle_bin rows: " . \App\Models\RecycleBin::count() . PHP_EOL;
