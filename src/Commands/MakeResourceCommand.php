<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeResourceCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Creates a Filament resource.';

    protected $signature = 'make:filament-resource {model}';

    public function handle()
    {
        $model = (string) Str::of($this->argument('model'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $modelClass = (string) Str::of($model)->afterLast('\\');
        $modelNamespace = (string) Str::of($model)->beforeLast('\\');
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
            'resource' => $resource,
            'resourceClass' => $resourceClass,
            'resourceNamespace' => $resourceNamespace,
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

        $this->info("Successfully created {$resourceClass}!");
    }
}
