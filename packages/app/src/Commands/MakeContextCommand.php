<?php

namespace Filament\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanValidateInput;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeContextCommand extends Command
{
    use CanManipulateFiles;
    use CanValidateInput;

    protected $description = 'Creates a Filament context.';

    protected $signature = 'make:filament-context {id?} {--F|force}';

    public function handle(): int
    {
        $id = Str::lcfirst($this->argument('id') ?? $this->askRequired('ID (e.g. `app`)', 'id'));

        $class = (string) str($id)
            ->ucfirst()
            ->append('ContextProvider');

        $path = app_path(
            (string) str($class)
                ->prepend('Providers/Filament/')
                ->replace('\\', '/')
                ->append('.php'),
        );

        if (! $this->option('force') && $this->checkForCollision([$path])) {
            return static::INVALID;
        }

        $this->copyStubToApp('ContextProvider', $path, [
            'class' => $class,
            'directory' => Str::ucfirst($id),
            'id' => $id,
        ]);

        $appConfig = file_get_contents(config_path('app.php'));

        if (! Str::contains($appConfig, "App\\Providers\\Filament\\{$class}::class")) {
            file_put_contents(config_path('app.php'), str_replace(
                'App\\Providers\\RouteServiceProvider::class,' . PHP_EOL,
                "App\\Providers\\Filament\\{$class}::class," . PHP_EOL . '        App\\Providers\\RouteServiceProvider::class,' . PHP_EOL,
                $appConfig,
            ));
        }

        $this->components->info("Successfully created {$class}!");

        return static::SUCCESS;
    }
}
