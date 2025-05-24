<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class FreshWithMediaClear extends Command
{
    protected $signature = 'migrate:fresh-with-media';
    protected $description = 'Run migrate:fresh and clear media files and records';

    public function handle()
    {
        $this->info('Clearing media...');
        Artisan::call('media:clear');
        $this->info(Artisan::output());

        $this->info('Running migrate:fresh...');
        Artisan::call('migrate:fresh');
        $this->info(Artisan::output());

        $this->info('Database and media cleared successfully.');
    }
}