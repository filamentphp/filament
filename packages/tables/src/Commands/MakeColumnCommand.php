<?php

namespace Filament\Tables\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\text;

#[AsCommand(name: 'make:table-column')]
class MakeColumnCommand extends Command
{
    use CanManipulateFiles;

    protected $description = 'Create a new table column class and cell view';

    protected $signature = 'make:table-column {name?} {--F|force}';

    public function handle(): int
    {
        $column = (string) str($this->argument('name') ?? text(
            label: 'What is the column name?',
            placeholder: 'StatusSwitcher',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $columnClass = (string) str($column)->afterLast('\\');
        $columnNamespace = str($column)->contains('\\') ?
            (string) str($column)->beforeLast('\\') :
            '';

        $view = str($column)
            ->prepend('tables\\columns\\')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) str($column)
                ->prepend('Tables\\Columns\\')
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

        $this->copyStubToApp('Column', $path, [
            'class' => $columnClass,
            'namespace' => 'App\\Tables\\Columns' . ($columnNamespace !== '' ? "\\{$columnNamespace}" : ''),
            'view' => $view,
        ]);

        if (! $this->fileExists($viewPath)) {
            $this->copyStubToApp('ColumnView', $viewPath);
        }

        $this->components->info("Filament table column [{$path}] created successfully.");

        return static::SUCCESS;
    }
}
