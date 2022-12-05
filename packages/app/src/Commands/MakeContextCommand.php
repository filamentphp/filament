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

        if (! $this->hasOption('force') && $this->checkForCollision([$path])) {
            return static::INVALID;
        }

        $this->copyStubToApp('ContextProvider', $path, [
            'class' => $class,
            'directory' => Str::ucfirst($id),
            'id' => $id,
        ]);

        $this->components->info("Successfully created {$class}!");

        $this->components->info('Make sure to register the service provider in `config/app.php`.');

        return static::SUCCESS;
    }
}
