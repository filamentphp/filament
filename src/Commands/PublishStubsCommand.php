<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class PublishStubsCommand extends Command
{
    protected $description = 'Publish Filament stubs';

    protected $signature = 'filament:stubs';

    public function handle()
    {
        $filesystem = new Filesystem();

        if (!is_dir($stubsPath = base_path('stubs'))) {
            $filesystem->makeDirectory($stubsPath);
        }

        $filesystem->copyDirectory(__DIR__ . '/../../stubs', $stubsPath . '/filament');

        $this->info('Successfully published filament stubs!');
    }
}
