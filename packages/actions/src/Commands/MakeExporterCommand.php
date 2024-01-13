<?php

namespace Filament\Actions\Commands;

use Filament\Support\Commands\Concerns\CanIndentStrings;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanReadModelSchemas;
use Illuminate\Console\Command;

use function Filament\Support\get_model_label;
use function Laravel\Prompts\text;

class MakeExporterCommand extends Command
{
    use CanIndentStrings;
    use CanManipulateFiles;
    use CanReadModelSchemas;
    use Concerns\CanGenerateExporterColumns;

    protected $description = 'Create a new Filament exporter class';

    protected $signature = 'make:filament-exporter {name?} {--G|generate} {--F|force}';

    public function handle(): int
    {
        $model = (string) str($this->argument('name') ?? text(
            label: 'What is the model name?',
            placeholder: 'BlogPost',
            required: true,
        ))
            ->studly()
            ->beforeLast('Exporter')
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->studly()
            ->replace('/', '\\');

        if (blank($model)) {
            $model = 'Exporter';
        }

        $modelClass = (string) str($model)->afterLast('\\');
        $modelNamespace = str($model)->contains('\\') ?
            (string) str($model)->beforeLast('\\') :
            '';

        $namespace = 'App\\Filament\\Exports';
        $path = app_path('Filament/Exports/');

        $exporter = "{$model}Exporter";
        $exporterClass = "{$modelClass}Exporter";
        $exporterNamespace = $modelNamespace;
        $namespace .= $exporterNamespace !== '' ? "\\{$exporterNamespace}" : '';

        $baseExporterPath =
            (string) str($exporter)
                ->prepend('/')
                ->prepend($path)
                ->replace('\\', '/')
                ->replace('//', '/');

        $exporterPath = "{$baseExporterPath}.php";

        if (! $this->option('force') && $this->checkForCollision([
            $exporterPath,
        ])) {
            return static::INVALID;
        }

        $this->copyStubToApp('Exporter', $exporterPath, [
            'columns' => $this->indentString($this->option('generate') ? $this->getExporterColumns(
                'App\\Models' . ($modelNamespace !== '' ? "\\{$modelNamespace}" : '') . '\\' . $modelClass,
            ) : '//', 3),
            'model' => $model === 'Exporter' ? 'Exporter as ExporterModel' : $model,
            'modelClass' => $model === 'Exporter' ? 'ExporterModel' : $modelClass,
            'modelLabel' => get_model_label($model),
            'namespace' => $namespace,
            'exporterClass' => $exporterClass,
        ]);

        $this->components->info("Filament exporter [{$exporterPath}] created successfully.");

        return static::SUCCESS;
    }
}
