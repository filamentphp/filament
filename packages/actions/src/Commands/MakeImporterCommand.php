<?php

namespace Filament\Actions\Commands;

use Filament\Support\Commands\Concerns\CanIndentStrings;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanReadModelSchemas;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

use function Filament\Support\get_model_label;
use function Laravel\Prompts\text;

#[AsCommand(name: 'make:filament-importer')]
class MakeImporterCommand extends Command
{
    use CanIndentStrings;
    use CanManipulateFiles;
    use CanReadModelSchemas;
    use Concerns\CanGenerateImporterColumns;

    protected $description = 'Create a new Filament importer class';

    protected $signature = 'make:filament-importer {name?} {--G|generate} {--F|force}';

    public function handle(): int
    {
        $model = (string) str($this->argument('name') ?? text(
            label: 'What is the model name?',
            placeholder: 'BlogPost',
            required: true,
        ))
            ->studly()
            ->beforeLast('Importer')
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->studly()
            ->replace('/', '\\');

        if (blank($model)) {
            $model = 'Importer';
        }

        $modelClass = (string) str($model)->afterLast('\\');
        $modelNamespace = str($model)->contains('\\') ?
            (string) str($model)->beforeLast('\\') :
            '';

        $namespace = 'App\\Filament\\Imports';
        $path = app_path('Filament/Imports/');

        $importer = "{$model}Importer";
        $importerClass = "{$modelClass}Importer";
        $importerNamespace = $modelNamespace;
        $namespace .= $importerNamespace !== '' ? "\\{$importerNamespace}" : '';

        $baseImporterPath =
            (string) str($importer)
                ->prepend('/')
                ->prepend($path)
                ->replace('\\', '/')
                ->replace('//', '/');

        $importerPath = "{$baseImporterPath}.php";

        if (! $this->option('force') && $this->checkForCollision([
            $importerPath,
        ])) {
            return static::INVALID;
        }

        $this->copyStubToApp('Importer', $importerPath, [
            'columns' => $this->indentString($this->option('generate') ? $this->getImporterColumns(
                'App\\Models' . ($modelNamespace !== '' ? "\\{$modelNamespace}" : '') . '\\' . $modelClass,
            ) : '//', 3),
            'model' => $model === 'Importer' ? 'Importer as ImporterModel' : $model,
            'modelClass' => $model === 'Importer' ? 'ImporterModel' : $modelClass,
            'modelLabel' => get_model_label($model),
            'namespace' => $namespace,
            'importerClass' => $importerClass,
        ]);

        $this->components->info("Filament importer [{$importerPath}] created successfully.");

        return static::SUCCESS;
    }
}
