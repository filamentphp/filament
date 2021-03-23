<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UpgradeCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Upgrade Filament to the latest version.';

    protected $signature = 'filament:upgrade';

    public function handle()
    {
        $commands = [
            'composer update',
            'php artisan migrate',
            'php artisan livewire:discover',
            'php artisan route:clear',
            'php artisan view:clear',
        ];

        $bar = $this->output->createProgressBar(count($commands));

        $bar->start();

        foreach ($commands as $command) {
            $this->newLine(2);
            system($command);

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        $this->info('Successfully upgraded!');
    }
}
