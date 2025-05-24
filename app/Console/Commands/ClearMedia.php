<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ClearMedia extends Command
{
    protected $signature = 'media:clear';
    protected $description = 'Clear all media files and database records';

    public function handle()
    {
        // Truncar la tabla media
        Media::truncate();
        $this->info('Media database records cleared.');

        // Eliminar carpetas numeradas en storage/app/public
        $mediaPath = storage_path('app/public');
        $directories = File::directories($mediaPath);
        foreach ($directories as $directory) {
            if (is_numeric(basename($directory))) {
                File::deleteDirectory($directory);
                $this->info("Deleted media directory: {$directory}");
            }
        }

        $this->info('Media files cleared successfully.');
    }
}