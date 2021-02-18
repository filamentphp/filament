<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeResourceCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Creates a Filament resource class and default action classes.';

    protected $signature = 'make:filament-resource {name}';

    public function handle()
    {
        $model = (string) Str::of($this->argument('name'))
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
        $indexResourceActionClass = "List{$pluralModelClass}";
        $createResourceActionClass = "Create{$modelClass}";
        $editResourceActionClass = "Edit{$modelClass}";

        $baseResourcePath = app_path(
            (string) Str::of($resource)
                ->prepend('Filament\\Resources\\')
                ->replace('\\', '/'),
        );
        $resourcePath = "{$baseResourcePath}.php";
        $resourceActionsDirectory = "{$baseResourcePath}/Actions";
        $indexResourceActionPath = "{$resourceActionsDirectory}/{$indexResourceActionClass}.php";
        $createResourceActionPath = "{$resourceActionsDirectory}/{$createResourceActionClass}.php";
        $editResourceActionPath = "{$resourceActionsDirectory}/{$editResourceActionClass}.php";

        if ($this->checkForCollision([
            $resourcePath,
            $indexResourceActionPath,
            $createResourceActionPath,
            $editResourceActionPath,
        ])) return;

        $this->copyStubToApp('Resource', $resourcePath, [
            'createResourceActionClass' => $createResourceActionClass,
            'editResourceActionClass' => $editResourceActionClass,
            'indexResourceActionClass' => $indexResourceActionClass,
            'model' => $model,
            'namespace' => 'App\Filament\Resources' . ($resourceNamespace !== '' ? "\\{$resourceNamespace}" : ''),
            'resource' => $resource,
            'resourceClass' => $resourceClass,
        ]);

        $this->copyStubToApp('ResourceAction', $indexResourceActionPath, [
            'baseResourceActionClass' => 'ListRecords',
            'resource' => $resource,
            'resourceClass' => $resourceClass,
            'resourceActionClass' => $indexResourceActionClass,
        ]);

        $this->copyStubToApp('ResourceAction', $createResourceActionPath, [
            'baseResourceActionClass' => 'CreateRecord',
            'resource' => $resource,
            'resourceClass' => $resourceClass,
            'resourceActionClass' => $createResourceActionClass,
        ]);

        $this->copyStubToApp('ResourceAction', $editResourceActionPath, [
            'baseResourceActionClass' => 'EditRecord',
            'resource' => $resource,
            'resourceClass' => $resourceClass,
            'resourceActionClass' => $editResourceActionClass,
        ]);

        $this->info("Successfully created {$resource}!");
    }
}
