<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeResourceCommand extends Command
{
    use Concerns\CanGenerateResources;
    use Concerns\CanManipulateFiles;
    use Concerns\CanValidateInput;

    protected $description = 'Creates a Filament resource class and default page classes.';

    protected $signature = 'make:filament-resource {name?} {--view-page} {--G|generate} {--S|simple} {--F|force}';

    public function handle(): int
    {
        $model = (string) Str::of($this->argument('name') ?? $this->askRequired('Model (e.g. `BlogPost`)', 'name'))
            ->studly()
            ->beforeLast('Resource')
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->studly()
            ->replace('/', '\\');
        $modelClass = (string) Str::of($model)->afterLast('\\');
        $modelNamespace = Str::of($model)->contains('\\') ?
            (string) Str::of($model)->beforeLast('\\') :
            '';
        $pluralModelClass = (string) Str::of($modelClass)->pluralStudly();

        $resource = "{$model}Resource";
        $resourceClass = "{$modelClass}Resource";
        $resourceNamespace = $modelNamespace;
        $listResourcePageClass = "List{$pluralModelClass}";
        $manageResourcePageClass = "Manage{$pluralModelClass}";
        $createResourcePageClass = "Create{$modelClass}";
        $editResourcePageClass = "Edit{$modelClass}";
        $viewResourcePageClass = "View{$modelClass}";

        $baseResourcePath = app_path(
            (string) Str::of($resource)
                ->prepend('Filament\\Resources\\')
                ->replace('\\', '/'),
        );
        $resourcePath = "{$baseResourcePath}.php";
        $resourcePagesDirectory = "{$baseResourcePath}/Pages";
        $listResourcePagePath = "{$resourcePagesDirectory}/{$listResourcePageClass}.php";
        $manageResourcePagePath = "{$resourcePagesDirectory}/{$manageResourcePageClass}.php";
        $createResourcePagePath = "{$resourcePagesDirectory}/{$createResourcePageClass}.php";
        $editResourcePagePath = "{$resourcePagesDirectory}/{$editResourcePageClass}.php";
        $viewResourcePagePath = "{$resourcePagesDirectory}/{$viewResourcePageClass}.php";

        if (! $this->option('force') && $this->checkForCollision([
            $resourcePath,
            $listResourcePagePath,
            $manageResourcePagePath,
            $createResourcePagePath,
            $editResourcePagePath,
            $viewResourcePagePath,
        ])) {
            return static::INVALID;
        }

        $pages = '';
        $pages .= '\'index\' => Pages\\' . ($this->option('simple') ? $manageResourcePageClass : $listResourcePageClass) . '::route(\'/\'),';

        if (! $this->option('simple')) {
            $pages .= PHP_EOL . "'create' => Pages\\{$createResourcePageClass}::route('/create'),";

            if ($this->option('view-page')) {
                $pages .= PHP_EOL . "'view' => Pages\\{$viewResourcePageClass}::route('/{record}'),";
            }

            $pages .= PHP_EOL . "'edit' => Pages\\{$editResourcePageClass}::route('/{record}/edit'),";
        }

        $relations = '';

        if (! $this->option('simple')) {
            $relations .= PHP_EOL . 'public static function getRelations(): array';
            $relations .= PHP_EOL . '{';
            $relations .= PHP_EOL . '    return [';
            $relations .= PHP_EOL . '        //';
            $relations .= PHP_EOL . '    ];';
            $relations .= PHP_EOL . '}' . PHP_EOL;
        }

        $this->copyStubToApp('Resource', $resourcePath, [
            'formSchema' => $this->option('generate') ? $this->getResourceFormSchema(
                ($modelNamespace !== '' ? $modelNamespace : 'App\Models') . '\\' . $modelClass
            ) : $this->indentString('//', 4),
            'model' => $model,
            'modelClass' => $modelClass,
            'namespace' => 'App\\Filament\\Resources' . ($resourceNamespace !== '' ? "\\{$resourceNamespace}" : ''),
            'resource' => $resource,
            'resourceClass' => $resourceClass,
            'tableColumns' => $this->option('generate') ? $this->getResourceTableColumns(
                ($modelNamespace !== '' ? $modelNamespace : 'App\Models') . '\\' . $modelClass
            ) : $this->indentString('//', 4),
            'pages' => $this->indentString($pages, 3),
            'relations' => $this->indentString($relations, 1),
        ]);

        if ($this->option('simple')) {
            $this->copyStubToApp('DefaultResourcePage', $manageResourcePagePath, [
                'baseResourcePage' => 'Filament\\Resources\\Pages\\ManageRecords',
                'baseResourcePageClass' => 'ManageRecords',
                'namespace' => "App\\Filament\\Resources\\{$resource}\\Pages",
                'resource' => $resource,
                'resourceClass' => $resourceClass,
                'resourcePageClass' => $manageResourcePageClass,
            ]);
        } else {
            $this->copyStubToApp('DefaultResourcePage', $listResourcePagePath, [
                'baseResourcePage' => 'Filament\\Resources\\Pages\\ListRecords',
                'baseResourcePageClass' => 'ListRecords',
                'namespace' => "App\\Filament\\Resources\\{$resource}\\Pages",
                'resource' => $resource,
                'resourceClass' => $resourceClass,
                'resourcePageClass' => $listResourcePageClass,
            ]);

            $this->copyStubToApp('DefaultResourcePage', $createResourcePagePath, [
                'baseResourcePage' => 'Filament\\Resources\\Pages\\CreateRecord',
                'baseResourcePageClass' => 'CreateRecord',
                'namespace' => "App\\Filament\\Resources\\{$resource}\\Pages",
                'resource' => $resource,
                'resourceClass' => $resourceClass,
                'resourcePageClass' => $createResourcePageClass,
            ]);

            if ($this->option('view-page')) {
                $this->copyStubToApp('DefaultResourcePage', $viewResourcePagePath, [
                    'baseResourcePage' => 'Filament\\Resources\\Pages\\ViewRecord',
                    'baseResourcePageClass' => 'ViewRecord',
                    'namespace' => "App\\Filament\\Resources\\{$resource}\\Pages",
                    'resource' => $resource,
                    'resourceClass' => $resourceClass,
                    'resourcePageClass' => $viewResourcePageClass,
                ]);
            }

            $this->copyStubToApp('DefaultResourcePage', $editResourcePagePath, [
                'baseResourcePage' => 'Filament\\Resources\\Pages\\EditRecord',
                'baseResourcePageClass' => 'EditRecord',
                'namespace' => "App\\Filament\\Resources\\{$resource}\\Pages",
                'resource' => $resource,
                'resourceClass' => $resourceClass,
                'resourcePageClass' => $editResourcePageClass,
            ]);
        }

        $this->info("Successfully created {$resource}!");

        return static::SUCCESS;
    }

    protected function indentString(string $string, int $level = 1): string
    {
        return implode(
            PHP_EOL,
            array_map(
                fn (string $line) => str_repeat('    ', $level) . "{$line}",
                explode(PHP_EOL, $string),
            ),
        );
    }
}
