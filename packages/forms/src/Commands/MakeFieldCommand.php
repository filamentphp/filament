<?php

namespace Filament\Forms\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-form-field', aliases: [
    'filament:field',
    'filament:form-field',
    'forms:field',
    'forms:make-field',
    'make:filament-field',
    'make:form-field',
])]
class MakeFieldCommand extends Command
{
    use CanManipulateFiles;

    protected $description = 'Create a new form field class and view';

    protected $signature = 'make:filament-form-field {name?} {--F|force}';

    /**
     * @var array<string>
     */
    protected $aliases = [
        'filament:field',
        'filament:form-field',
        'forms:field',
        'forms:make-field',
        'make:filament-field',
        'make:form-field',
    ];

    public function handle(): int
    {
        $field = (string) str($this->argument('name') ?? text(
            label: 'What is the field name?',
            placeholder: 'RangeSlider',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $fieldClass = (string) str($field)->afterLast('\\');
        $fieldNamespace = str($field)->contains('\\') ?
            (string) str($field)->beforeLast('\\') :
            '';

        $view = str($field)
            ->prepend('filament\\forms\\components\\')
            ->explode('\\')
            ->map(static fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) str($field)
                ->prepend('Filament\\Forms\\Components\\')
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

        $this->copyStubToApp('Field', $path, [
            'class' => $fieldClass,
            'namespace' => 'App\\Filament\\Forms\\Components' . ($fieldNamespace !== '' ? "\\{$fieldNamespace}" : ''),
            'view' => $view,
        ]);

        if (! $this->fileExists($viewPath)) {
            $this->copyStubToApp('FieldView', $viewPath);
        }

        $this->components->info("Filament form field [{$path}] created successfully.");

        return static::SUCCESS;
    }
}
