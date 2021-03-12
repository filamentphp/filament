<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeResourceCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $description = 'Creates a Filament resource class and default page classes.';

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
        $indexResourcePageClass = "List{$pluralModelClass}";
        $createResourcePageClass = "Create{$modelClass}";
        $editResourcePageClass = "Edit{$modelClass}";

        $baseResourcePath = app_path(
            (string) Str::of($resource)
                ->prepend('Filament\\Resources\\')
                ->replace('\\', '/'),
        );
        $resourcePath = "{$baseResourcePath}.php";
        $resourcePagesDirectory = "{$baseResourcePath}/Pages";
        $indexResourcePagePath = "{$resourcePagesDirectory}/{$indexResourcePageClass}.php";
        $createResourcePagePath = "{$resourcePagesDirectory}/{$createResourcePageClass}.php";
        $editResourcePagePath = "{$resourcePagesDirectory}/{$editResourcePageClass}.php";

        if ($this->checkForCollision([
            $resourcePath,
            $indexResourcePagePath,
            $createResourcePagePath,
            $editResourcePagePath,
        ])) return;

        $this->copyStubToApp('Resource', $resourcePath, [
            'createResourcePageClass' => $createResourcePageClass,
            'editResourcePageClass' => $editResourcePageClass,
            'indexResourcePageClass' => $indexResourcePageClass,
            'namespace' => 'App\\Filament\\Resources' . ($resourceNamespace !== '' ? "\\{$resourceNamespace}" : ''),
            'resource' => $resource,
            'resourceClass' => $resourceClass,
        ]);

        $this->copyStubToApp('DefaultResourcePage', $indexResourcePagePath, [
            'baseResourcePage' => 'Filament\\Resources\\Pages\\ListRecords',
            'baseResourcePageClass' => 'ListRecords',
            'namespace' => "App\\Filament\\Resources\\{$resource}\\Pages",
            'resource' => $resource,
            'resourceClass' => $resourceClass,
            'resourcePageClass' => $indexResourcePageClass,
        ]);

        $this->copyStubToApp('DefaultResourcePage', $createResourcePagePath, [
            'baseResourcePage' => 'Filament\\Resources\\Pages\\CreateRecord',
            'baseResourcePageClass' => 'CreateRecord',
            'namespace' => "App\\Filament\\Resources\\{$resource}\\Pages",
            'resource' => $resource,
            'resourceClass' => $resourceClass,
            'resourcePageClass' => $createResourcePageClass,
        ]);

        $this->copyStubToApp('DefaultResourcePage', $editResourcePagePath, [
            'baseResourcePage' => 'Filament\\Resources\\Pages\\EditRecord',
            'baseResourcePageClass' => 'EditRecord',
            'namespace' => "App\\Filament\\Resources\\{$resource}\\Pages",
            'resource' => $resource,
            'resourceClass' => $resourceClass,
            'resourcePageClass' => $editResourcePageClass,
        ]);

        $this->info("Successfully created {$resource}!");
    }
}
