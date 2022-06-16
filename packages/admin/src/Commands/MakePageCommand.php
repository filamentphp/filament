<?php

namespace Filament\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakePageCommand extends Command
{
    use Concerns\CanManipulateFiles;
    use Concerns\CanValidateInput;
    use Concerns\CanTransposeStringPath;

    protected $description = 'Creates a Filament page class and view.';

    protected $signature = 'make:filament-page {name?} {--R|resource=} {--T|type=} {--F|force}';

    public function handle(): int
    {
        $page = (string)Str::of($this->argument('name') ?? $this->askRequired('Name (e.g. `Settings`)', 'name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $page = (string)Str::of($this->argument('name') ?? $this->askRequired('Name (e.g. `Settings`)', 'name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $pageClass = (string)Str::of($page)->afterLast('\\');
        $pageNamespace = Str::of($page)->contains('\\') ?
            (string)Str::of($page)->beforeLast('\\') :
            '';

        $resource = null;
        $resourceClass = null;
        $resourcePage = null;

        $resourceInput = $this->option('resource') ?? $this->ask('(Optional) Resource (e.g. `UserResource`)');




        if ($resourceInput !== null) {
            $resource = (string)Str::of($resourceInput)
                ->studly()
                ->trim('/')
                ->trim('\\')
                ->trim(' ')
                ->replace('/', '\\');

            if (!Str::of($resource)->endsWith('Resource')) {
                $resource .= 'Resource';
            }

            $resourceClass = (string)Str::of($resource)
                ->afterLast('\\');

            $resourcePage = $this->option('type') ?? $this->choice(
                    'Which type of page would you like to create?',
                    [
                        'custom' => 'Custom',
                        'ListRecords' => 'List',
                        'CreateRecord' => 'Create',
                        'EditRecord' => 'Edit',
                        'ViewRecord' => 'View',
                        'ManageRecords' => 'Manage',
                    ],
                    'custom',
                );
        }

        $resourcePath = config('filament.resources.path');
        if ($resourcePath) {
            if (preg_match('/[$][a-zA-Z]*[$]/', config('filament.resources.path'))) {
                $resourcePath = (string)Str::of(preg_replace('/[$][a-zA-Z]*[$]/', $resource, config('filament.resources.path')))
                    ->replace('//', '/');
            }
        }
        $resourceNamespace = (config('filament.resources.namespace'));

        if ($resourceNamespace) {
            if (preg_match('/[$][a-zA-Z]*[$]/', config('filament.resources.namespace'))) {
                $resourceNamespace = (string)Str::of(preg_replace('/[$][a-zA-Z]*[$]/', $resource, config('filament.resources.namespace')))
                    ->replace('\\\\', '\\');
            }

        }

        $view = Str::of($page)
            ->prepend($resource === null ? $this->changeForwardSlashToBackSlashes(Str::lower(config('filament.pages.path'))) : $this->changeForwardSlashToBackSlashes(Str::lower($resourcePath)))
            ->explode('\\')
            ->map(fn($segment) => Str::kebab($segment))
            ->implode('.');


        $path = app_path(
            (string)Str::of($page)
                ->prepend($resource === null ? $this->changeForwardSlashToBackSlashes(config('filament.pages.path')) : $this->changeForwardSlashToBackSlashes($resourcePath))
                ->replace('\\', '/')
                ->append('.php'),
        );
        $viewPath = resource_path(
            (string)Str::of($view)
                ->replace('.', '/')
                ->prepend('views/')
                ->append('.blade.php'),
        );

        $files = array_merge(
            [$path],
            $resourcePage === 'custom' ? [$viewPath] : [],
        );

        if (!$this->option('force') && $this->checkForCollision($files)) {
            return static::INVALID;
        }


        if ($resource === null) {
            $this->copyStubToApp('Page', $path, [
                'class' => $pageClass,
                'namespace' => config('filament.pages.namespace') . ($pageNamespace !== '' ? "\\{$pageNamespace}" : ''),
                'view' => $view,
            ]);
        } else {
            $this->copyStubToApp($resourcePage === 'custom' ? 'CustomResourcePage' : 'ResourcePage', $path, [
                'baseResourcePage' => 'Filament\\Resources\\Pages\\' . ($resourcePage === 'custom' ? 'Page' : $resourcePage),
                'baseResourcePageClass' => $resourcePage === 'custom' ? 'Page' : $resourcePage,
                'namespace' => $resourceNamespace . ($pageNamespace !== '' ? "\\{$pageNamespace}" : ''),
                'resource' => $resource,
                'resourceClass' => $resourceClass,
                'resourcePageClass' => $pageClass,
                'view' => $view,
            ]);
        }

        if ($resource === null || $resourcePage === 'custom') {
            $this->copyStubToApp('PageView', $viewPath);
        }

        $this->info("Successfully created {$page}!");

        if ($resource !== null) {
            $this->info("Make sure to register the page in `{$resourceClass}::getPages()`.");
        }

        return static::SUCCESS;
    }
}
