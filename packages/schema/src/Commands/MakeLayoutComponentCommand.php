<?php

namespace Filament\Schema\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-schema-layout', aliases: [
    'filament:layout',
    'filament:form-layout',
    'filament:infolist-layout',
    'forms:layout',
    'forms:make-layout',
    'infolists:layout',
    'infolists:make-layout',
    'make:filament-layout',
    'make:infolist-layout',
    'make:form-layout',
])]
class MakeLayoutComponentCommand extends Command
{
    use CanManipulateFiles;

    protected $description = 'Create a new schema layout component class and view';

    protected $signature = 'make:filament-schema-layout {name?} {--F|force}';

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:layout',
        'filament:form-layout',
        'filament:infolist-layout',
        'forms:layout',
        'forms:make-layout',
        'infolists:layout',
        'infolists:make-layout',
        'make:filament-layout',
        'make:infolist-layout',
        'make:form-layout',
    ];

    public function handle(): int
    {
        $component = (string) str($this->argument('name') ?? text(
            label: 'What is the layout name?',
            placeholder: 'Wizard',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $componentClass = (string) str($component)->afterLast('\\');
        $componentNamespace = str($component)->contains('\\') ?
            (string) str($component)->beforeLast('\\') :
            '';

        $view = str($component)
            ->prepend('filament\\schema\\components\\')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) str($component)
                ->prepend('Filament\\Schema\\Components\\')
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

        $this->copyStubToApp('LayoutComponent', $path, [
            'class' => $componentClass,
            'namespace' => 'App\\Filament\\Schema\\Components' . ($componentNamespace !== '' ? "\\{$componentNamespace}" : ''),
            'view' => $view,
        ]);

        if (! $this->fileExists($viewPath)) {
            $this->copyStubToApp('LayoutComponentView', $viewPath);
        }

        $this->components->info("Filament schema layout component [{$path}] created successfully.");

        return static::SUCCESS;
    }
}
