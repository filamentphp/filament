<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeResourceCommand extends Command
{
    use Concerns\CanGenerateResources;
    use Concerns\CanIndentStrings;
    use Concerns\CanManipulateFiles;
    use Concerns\CanValidateInput;

    public string $editResourcePageClass;

    public string $manageResourcePageClass;

    public string $createResourcePageClass;

    public string $listResourcePageClass;

    public string $viewResourcePageClass;

    protected $description = 'Creates a Filament resource class and default page classes.';

    protected $signature = 'make:filament-resource {name?} {--soft-deletes} {--view} {--G|generate} {--S|simple} {--F|force}';

    public function handle(): int
    {
        $model = $this->getModel();

        $modelClass = (string) Str::of($model)->afterLast('\\');

        $modelNamespace = $this->getModelNamespace($model);

        $pluralModelClass = (string) Str::of($modelClass)->pluralStudly();

        $resource = "{$model}Resource";
        $resourceClass = "{$modelClass}Resource";
        $resourceNamespace = $modelNamespace;

        $this->listResourcePageClass = "List{$pluralModelClass}";
        $this->manageResourcePageClass = "Manage{$pluralModelClass}";
        $this->createResourcePageClass = "Create{$modelClass}";
        $this->editResourcePageClass = "Edit{$modelClass}";
        $this->viewResourcePageClass = "View{$modelClass}";

        $baseResourcePath = $this->getBaseResourcePath($resource);

        $resourcePath = "{$baseResourcePath}.php";
        $resourcePagesDirectory = "{$baseResourcePath}/Pages";
        $listResourcePagePath = "{$resourcePagesDirectory}/{$this->listResourcePageClass}.php";
        $manageResourcePagePath = "{$resourcePagesDirectory}/{$this->manageResourcePageClass}.php";
        $createResourcePagePath = "{$resourcePagesDirectory}/{$this->createResourcePageClass}.php";
        $editResourcePagePath = "{$resourcePagesDirectory}/{$this->editResourcePageClass}.php";
        $viewResourcePagePath = "{$resourcePagesDirectory}/{$this->viewResourcePageClass}.php";

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

        $pages = $this->pagesCode();

        $tableActions = $this->tableActionsCode();

        $relations = $this->relationsCode();

        $tableBulkActions = $this->tableBulkActionsCode();

        $eloquentQuery = $this->eloquentQueryCode();

        $this->copyStubToApp('Resource', $resourcePath, [
            'eloquentQuery' => $this->indentString($eloquentQuery, 1),
            'formSchema' => $this->option('generate') ? $this->getResourceFormSchema(
                ($modelNamespace !== '' ? $modelNamespace : 'App\Models') . '\\' . $modelClass,
            ) : $this->indentString('//', 4),
            'model' => $model === 'Resource' ? 'Resource as ResourceModel' : $model,
            'modelClass' => $model === 'Resource' ? 'ResourceModel' : $modelClass,
            'namespace' => 'App\\Filament\\Resources' . ($resourceNamespace !== '' ? "\\{$resourceNamespace}" : ''),
            'pages' => $this->indentString($pages, 3),
            'relations' => $this->indentString($relations, 1),
            'resource' => $resource,
            'resourceClass' => $resourceClass,
            'tableActions' => $this->indentString($tableActions, 4),
            'tableBulkActions' => $this->indentString($tableBulkActions, 4),
            'tableColumns' => $this->option('generate') ? $this->getResourceTableColumns(
                ($modelNamespace !== '' ? $modelNamespace : 'App\Models') . '\\' . $modelClass
            ) : $this->indentString('//', 4),
            'tableFilters' => $this->indentString(
                $this->option('soft-deletes') ? 'Tables\Filters\TrashedFilter::make(),' : '//',
                4,
            ),
        ]);

        if ($this->option('simple')) {
            $this->copyStubToApp('ResourceManagePage', $manageResourcePagePath, [
                'namespace' => "App\\Filament\\Resources\\{$resource}\\Pages",
                'resource' => $resource,
                'resourceClass' => $resourceClass,
                'resourcePageClass' => $this->manageResourcePageClass,
            ]);
        } else {
            $this->copyStubToApp('ResourceListPage', $listResourcePagePath, [
                'namespace' => "App\\Filament\\Resources\\{$resource}\\Pages",
                'resource' => $resource,
                'resourceClass' => $resourceClass,
                'resourcePageClass' => $this->listResourcePageClass,
            ]);

            $this->copyStubToApp('ResourcePage', $createResourcePagePath, [
                'baseResourcePage' => 'Filament\\Resources\\Pages\\CreateRecord',
                'baseResourcePageClass' => 'CreateRecord',
                'namespace' => "App\\Filament\\Resources\\{$resource}\\Pages",
                'resource' => $resource,
                'resourceClass' => $resourceClass,
                'resourcePageClass' => $this->createResourcePageClass,
            ]);

            $editPageActions = [];

            if ($this->option('view')) {
                $this->copyStubToApp('ResourceViewPage', $viewResourcePagePath, [
                    'namespace' => "App\\Filament\\Resources\\{$resource}\\Pages",
                    'resource' => $resource,
                    'resourceClass' => $resourceClass,
                    'resourcePageClass' => $this->viewResourcePageClass,
                ]);

                $editPageActions[] = 'Actions\ViewAction::make(),';
            }

            $editPageActions[] = 'Actions\DeleteAction::make(),';

            if ($this->option('soft-deletes')) {
                $editPageActions[] = 'Actions\ForceDeleteAction::make(),';
                $editPageActions[] = 'Actions\RestoreAction::make(),';
            }

            $editPageActions = implode(PHP_EOL, $editPageActions);

            $this->copyStubToApp('ResourceEditPage', $editResourcePagePath, [
                'actions' => $this->indentString($editPageActions, 3),
                'namespace' => "App\\Filament\\Resources\\{$resource}\\Pages",
                'resource' => $resource,
                'resourceClass' => $resourceClass,
                'resourcePageClass' => $this->editResourcePageClass,
            ]);
        }

        $this->info("Successfully created {$resource}!");

        return static::SUCCESS;
    }

    public function getModel()
    {
        $model = (string) Str::of($this->argument('name') ?? $this->askRequired('Model (e.g. `BlogPost`)', 'name'))
            ->studly()
            ->beforeLast('Resource')
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->studly()
            ->replace('/', '\\');

        return ! blank($model) ? $model : 'Resource';
    }

    public function getModelNamespace($model)
    {
        return Str::of($model)->contains('\\') ?
            (string) Str::of($model)->beforeLast('\\') :
            '';
    }

    public function getBaseResourcePath($resource)
    {
        return app_path(
            (string) Str::of($resource)
                ->prepend('Filament\\Resources\\')
                ->replace('\\', '/'),
        );
    }

    public function pagesCode()
    {
        $pages = '';
        $pages .= '\'index\' => Pages\\' . ($this->option('simple') ? $this->manageResourcePageClass : $this->listResourcePageClass) . '::route(\'/\'),';

        if (! $this->option('simple')) {
            $pages .= PHP_EOL . "'create' => Pages\\{$this->createResourcePageClass}::route('/create'),";

            if ($this->option('view')) {
                $pages .= PHP_EOL . "'view' => Pages\\{$this->viewResourcePageClass}::route('/{record}'),";
            }

            $pages .= PHP_EOL . "'edit' => Pages\\{$this->editResourcePageClass}::route('/{record}/edit'),";
        }

        return $pages;
    }

    public function tableActionsCode()
    {
        $tableActions = [];

        if ($this->option('view')) {
            $tableActions[] = 'Tables\Actions\ViewAction::make(),';
        }

        $tableActions[] = 'Tables\Actions\EditAction::make(),';

        if ($this->option('simple')) {
            $tableActions[] = 'Tables\Actions\DeleteAction::make(),';

            if ($this->option('soft-deletes')) {
                $tableActions[] = 'Tables\Actions\ForceDeleteAction::make(),';
                $tableActions[] = 'Tables\Actions\RestoreAction::make(),';
            }
        }

        return implode(PHP_EOL, $tableActions);
    }

    public function tableBulkActionsCode()
    {
        $tableBulkActions = [];

        $tableBulkActions[] = 'Tables\Actions\DeleteBulkAction::make(),';

        if ($this->option('soft-deletes')) {
            $tableBulkActions[] = 'Tables\Actions\RestoreBulkAction::make(),';
            $tableBulkActions[] = 'Tables\Actions\ForceDeleteBulkAction::make(),';
        }

        return implode(PHP_EOL, $tableBulkActions);
    }

    public function relationsCode()
    {
        $relations = '';

        if (! $this->option('simple')) {
            $relations .= PHP_EOL . 'public static function getRelations(): array';
            $relations .= PHP_EOL . '{';
            $relations .= PHP_EOL . '    return [';
            $relations .= PHP_EOL . '        //';
            $relations .= PHP_EOL . '    ];';
            $relations .= PHP_EOL . '}' . PHP_EOL;
        }

        return $relations;
    }

    public function eloquentQueryCode()
    {
        $eloquentQuery = '';
        if ($this->option('soft-deletes')) {
            $eloquentQuery .= PHP_EOL . PHP_EOL . 'public static function getEloquentQuery(): Builder';
            $eloquentQuery .= PHP_EOL . '{';
            $eloquentQuery .= PHP_EOL . '    return parent::getEloquentQuery()';
            $eloquentQuery .= PHP_EOL . '        ->withoutGlobalScopes([';
            $eloquentQuery .= PHP_EOL . '            SoftDeletingScope::class,';
            $eloquentQuery .= PHP_EOL . '        ]);';
            $eloquentQuery .= PHP_EOL . '}';
        }

        return $eloquentQuery;
    }
}
