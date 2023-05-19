<?php

namespace Filament\Infolists\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanValidateInput;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeEntryCommand extends Command
{
    use CanManipulateFiles;
    use CanValidateInput;

    protected $description = 'Creates an infolist entry class and view.';

    protected $signature = 'make:infolist-entry {name?} {--F|force}';

    public function handle(): int
    {
        $entry = (string) str($this->argument('name') ?? $this->askRequired('Name (e.g. `StatusSwitcher`)', 'name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $entryClass = (string) str($entry)->afterLast('\\');
        $entryNamespace = str($entry)->contains('\\') ?
            (string) str($entry)->beforeLast('\\') :
            '';

        $view = str($entry)
            ->prepend('infolists\\components\\')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) str($entry)
                ->prepend('Infolists\\Components\\')
                ->replace('\\', '/')
                ->append('.php'),
        );
        $viewPath = resource_path(
            (string) str($view)
                ->replace('.', '/')
                ->prepend('views/')
                ->append('.blade.php'),
        );

        if (! $this->option('force') && $this->checkForCollision([
            $path,
        ])) {
            return static::INVALID;
        }

        $this->copyStubToApp('Entry', $path, [
            'class' => $entryClass,
            'namespace' => 'App\\Infolists\\Components' . ($entryNamespace !== '' ? "\\{$entryNamespace}" : ''),
            'view' => $view,
        ]);

        if (! $this->fileExists($viewPath)) {
            $this->copyStubToApp('EntryView', $viewPath);
        }

        $this->components->info("Successfully created {$entry}!");

        return static::SUCCESS;
    }
}
