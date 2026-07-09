<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$rows = [
    ['migration' => '2024_01_01_000000_create_base_tables', 'batch' => 13],
    ['migration' => '2026_06_22_000001_add_konfirmasi_datetime_to_events_table', 'batch' => 14],
    ['migration' => '2026_06_28_000000_create_questions_table', 'batch' => 15],
];

foreach ($rows as $row) {
    $exists = \DB::table('migrations')->where('migration', $row['migration'])->exists();
    if (!$exists) {
        \DB::table('migrations')->insert($row);
        echo "Inserted: {$row['migration']}\n";
    } else {
        echo "Already present: {$row['migration']}\n";
    }
}
