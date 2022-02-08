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

    protected $signature = 'make:filament-resource {model?} {--N|name=} {--G|generate} {--S|simple}';

    public function handle(): int
    {
        $model = (string) Str::of($this->argument('model') ?? $this->askRequired('Model (e.g. `BlogPost`)', 'model'))
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

        $resourceName = (string) Str::of($this->option('name') ?? $model)
            ->studly()
            ->beforeLast('Resource')
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->studly()
            ->replace('/', '\\');
        $pluralResourceName = (string) Str::of($resourceName)->pluralStudly();

        $resource = "{$resourceName}Resource";
        $resourceClass = "{$resourceName}Resource";
        $resourceNamespace = $modelNamespace;
        $listResourcePageClass = "List{$pluralResourceName}";
        $manageResourcePageClass = "Manage{$pluralResourceName}";
        $createResourcePageClass = "Create{$resourceName}";
        $editResourcePageClass = "Edit{$resourceName}";

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

        $resourceNameParts = $this->option('name') ? <<<RNP

    protected static ?string \$slug = '$resourceName';

    protected static ?string \$label = '$resourceName';

    protected static ?string \$pluralLabel = '$pluralResourceName';

RNP : '';

        if ($this->checkForCollision([
            $resourcePath,
            $listResourcePagePath,
            $manageResourcePagePath,
            $createResourcePagePath,
            $editResourcePagePath,
        ])) {
            return static::INVALID;
        }

        $this->copyStubToApp($this->option('simple') ? 'SimpleResource' : 'Resource', $resourcePath, [
            'createResourcePageClass' => $createResourcePageClass,
            'editResourcePageClass' => $editResourcePageClass,
            'formSchema' => $this->option('generate') ? $this->getResourceFormSchema("App\Models\\{$model}") : $this->indentString('//'),
            'indexResourcePageClass' => $this->option('simple') ? $manageResourcePageClass : $listResourcePageClass,
            'model' => $model,
            'modelClass' => $modelClass,
            'namespace' => 'App\\Filament\\Resources' . ($resourceNamespace !== '' ? "\\{$resourceNamespace}" : ''),
            'resource' => $resource,
            'resourceClass' => $resourceClass,
            'tableColumns' => $this->option('generate') ? $this->getResourceTableColumns("App\Models\\{$model}") : $this->indentString('//'),
            'resourceNameParts' => $resourceNameParts,
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
}
