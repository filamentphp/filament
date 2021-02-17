<?php

namespace Filament\Commands;

use Filament\Models\FilamentUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MakeResourceCommand extends Command
{
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

        $collision = $this->checkForCollision([
            $resourcePath,
            $indexResourceActionPath,
            $createResourceActionPath,
            $editResourceActionPath,
        ]);

        if ($collision !== false) {
            $this->error("$collision already exists, aborting.");

            return;
        }

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

    protected function checkForCollision($paths)
    {
        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return false;
    }

    protected function copyStubToApp($stub, $targetPath, $replacements = [])
    {
        $stub = Str::of(file_get_contents(__DIR__."/../../stubs/{$stub}.stub"));

        foreach ($replacements as $key => $replacement) {
            $stub = $stub->replace("{{{$key}}}", $replacement);
        }

        $stub = (string) $stub;

        $this->writeFile($targetPath, $stub);
    }

    protected function writeFile($path, $contents)
    {
        $currentDirectory = '';

        Str::of($path)
            ->explode('/')
            ->slice(0, -1)
            ->each(function ($directory) use (&$currentDirectory) {
                $currentDirectory .= "/{$directory}";

                if (is_dir($currentDirectory)) return;

                mkdir($currentDirectory);
            });

        file_put_contents($path, $contents);
    }
}
