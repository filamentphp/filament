<?php

namespace Filament\Commands;

use Filament\Support\Commands\Concerns\CanInstallUpgradeCommand;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\UpgradeCommand;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    use CanManipulateFiles;
    use CanInstallUpgradeCommand;

    protected $signature = 'filament:install {--F|force}';

    protected $description = 'Install Filament.';

    public function __invoke(): int
    {
        $this->installDefaultContext();

        $this->call(UpgradeCommand::class);

        $this->installUpgradeCommand();

        if ($this->confirm('All done! Would you like to show some love by starring the Filament repo on GitHub?', true)) {
            if (PHP_OS_FAMILY === 'Darwin') {
                exec('open https://github.com/filamentphp/filament');
            }
            if (PHP_OS_FAMILY === 'Linux') {
                exec('xdg-open https://github.com/filamentphp/filament');
            }
            if (PHP_OS_FAMILY === 'Windows') {
                exec('start https://github.com/filamentphp/filament');
            }

            $this->components->info('Thank you!');
        }

        return static::SUCCESS;
    }

    protected function installDefaultContext()
    {
        $path = app_path('Providers/Filament/AdminFilamentProvider.php');

        if (! $this->option('force') && $this->checkForCollision([$path])) {
            return static::INVALID;
        }

        if (! Str::contains($appConfig = file_get_contents(config_path('app.php')), 'App\\Providers\\Filament\\AdminFilamentProvider::class')) {
            file_put_contents(config_path('app.php'), str_replace(
                'App\\Providers\\RouteServiceProvider::class,' . PHP_EOL,
                'App\\Providers\\RouteServiceProvider::class,' . PHP_EOL . '        App\\Providers\\Filament\\AdminFilamentProvider::class,' . PHP_EOL,
                $appConfig,
            ));
        }

        $this->components->info('Successfully created AdminProvider.php!');
    }
}
