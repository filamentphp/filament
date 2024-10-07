<?php

namespace Filament\Infolists\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-infolist-entry', aliases: [
    'filament:entry',
    'filament:infolist-entry',
    'infolists:entry',
    'infolists:make-entry',
    'make:filament-entry',
    'make:infolist-entry',
])]
class MakeEntryCommand extends Command
{
    use CanManipulateFiles;

    protected $description = 'Create a new infolist entry class and view';

    protected $signature = 'make:filament-infolist-entry {name?} {--F|force}';

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:entry',
        'filament:infolist-entry',
        'infolists:entry',
        'infolists:make-entry',
        'make:filament-entry',
        'make:infolist-entry',
    ];

    public function handle(): int
    {
        $entry = (string) str($this->argument('name') ?? text(
            label: 'What is the entry name?',
            placeholder: 'StatusSwitcher',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $entryClass = (string) str($entry)->afterLast('\\');
        $entryNamespace = str($entry)->contains('\\') ?
            (string) str($entry)->beforeLast('\\') :
            '';

        $view = str($entry)
            ->prepend('filament\\infolists\\components\\')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) str($entry)
                ->prepend('Filament\\Infolists\\Components\\')
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
            'namespace' => 'App\\Filament\\Infolists\\Components' . ($entryNamespace !== '' ? "\\{$entryNamespace}" : ''),
            'view' => $view,
        ]);

        if (! $this->fileExists($viewPath)) {
            $this->copyStubToApp('EntryView', $viewPath);
        }

        $this->components->info("Filament infolist entry [{$path}] created successfully.");

        return static::SUCCESS;
    }
}
