<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class MigrateBuktiUndangan extends Command
{
    protected $signature = 'bukti:migrate';
    protected $description = 'Migrate bukti_undangan files from public/storage to storage/app/public';

    public function handle()
    {
        $source = public_path('storage/bukti_undangan');
        $dest = storage_path('app/public/bukti_undangan');

        if (!File::isDirectory($source)) {
            $this->info('Source directory does not exist: ' . $source);
            return 0;
        }

        if (!File::isDirectory($dest)) {
            File::makeDirectory($dest, 0755, true);
            $this->info('Created destination directory');
        }

        $files = File::files($source);
        $count = 0;

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $destPath = $dest . '/' . $filename;

            if (Storage::disk('public')->exists('bukti_undangan/' . $filename)) {
                $this->line('Skipped (exists): ' . $filename);
                continue;
            }

            Storage::disk('public')->putFileAs('bukti_undangan', $file, $filename);
            $this->info('Migrated: ' . $filename);
            $count++;
        }

        $this->info("Done! Migrated {$count} files.");
        return 0;
    }
}