<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$source = public_path('storage/bukti_undangan');
$dest = storage_path('app/public/bukti_undangan');

echo "Source: $source\n";
echo "Dest: $dest\n\n";

if (!is_dir($source)) {
    echo "Source directory does not exist.\n";
    exit(1);
}

if (!is_dir($dest)) {
    mkdir($dest, 0755, true);
    echo "Created destination directory.\n";
}

$files = scandir($source);
$count = 0;

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    
    $srcPath = $source . '/' . $file;
    $destPath = $dest . '/' . $file;
    
    if (is_file($destPath)) {
        echo "Skipped (exists): $file\n";
        continue;
    }
    
    copy($srcPath, $destPath);
    echo "Migrated: $file\n";
    $count++;
}

echo "\nDone! Migrated $count files.\n";